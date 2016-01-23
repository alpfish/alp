<?php
namespace Alp\Auth\Providers;

use Auth;
use Socialite;
use Illuminate\Routing\Controller;
use App\Models\UserSystem\UserProvider\UserProvider;

/* *
 * 配置注意：
 * 1. 将社交登录服务提供商授权后的回调统一指向：self::handleProviderCallback($provider)控制器处理的路由
 * 2. 用$provider判断服务提供商: qq, weixin, github
 *
 * 将相关路由指向：
 * 1. 社交登录入口控制器（重定向）
 * 2. 社交登录回调控制器（数据处理及登录）
 *
 * */
class SocialAuth extends Controller
{
    /* *
     * 路由调度
     *
     * 必须将路由设置在web中间件内，否则session等错误而无法使用
     *
     * Route::group(['middleware' => 'web'], function () {
     *      Route::get('auth/{provider}/{back?}', '\Alp\Auth\Providers\SocialAuth@router');
     * });
     *
     * */
    public function router($provider, $back = null)
    {
        return is_null($back) ? $this->redirectToProvider($provider) : $this->handleProviderCallback($provider);
    }

    /*
     * 授权路由
     *
     * @return Redirect Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /*
     * 回调路由
     *
     * @param  string $provider 社交登录提供商
     * @return Redirect Response
     */
    public function handleProviderCallback($provider)
    {
        // 耗时瓶颈在于这里的Socialite(占用4-5秒)
        $socialUser = Socialite::driver($provider)->user();
        return $this->loginThirdParty($socialUser, $provider);
    }

    /*
     * 社交登录回调处理
     *
     * @param  object $socialUser  登录成功后回调的用户数据接口
     * @param  string $provider 社交登录提供商
     * @return Redirect Response
     */
    public function loginThirdParty($socialUser, $provider)
    {
        /*
         * 查找|更新|创建 社交用户
         */
        $user = $this->findUserOrCreate($socialUser, $provider);

        /*
         * 检查禁用
         */
        if ($user->status != 1)
            return redirect()->back()
                ->withErrors(['user_provider' => '对不起！您的帐号未激活，请联系我们。']);

        // 这里是一次登录还是cookie session登录？
        //Auth::loginUsingId($user->id);
        /* *
         * 登录响应
         * 1. 重定向视图响应，怎么传JWT Token？
         * 2. API JSON响应， 可行否？
         * */
        dd('TODO');
    }


    /* *
     * 更新社交用户信息
     *
     * @param  object $socialUser 登录的社交用户
     * @param  string $provider 社交服务提供商
     * @return object 返回的是User模型，非UserProvider
     */
    public function findUserOrCreate($socialUser, $provider)
    {
        // 找到
        if ($user_provider = UserProvider::whereProviderId($socialUser->getId())
            ->whereProvider($provider)
            ->first()) {
            // 检查更新后返回User模型
            return $this->checkUpdate($user_provider, $socialUser);
        }

        // 未找到，创建只有自增ID的User帐号
        $user = model('User')->create([NULL]);

        // 关联创建UserProvider
        $user->providers()->create([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'name' => empty($socialUser->getNickname()) ? $socialUser->getName() : $socialUser->getNickname(),
            'avatar' => $socialUser->getAvatar(),
        ]);

        return $user;
    }

    /* *
     * 更新社交用户信息
     *
     * @param  object $user_provider 存储的用户
     * @param  object $socialUser 登录的用户
     * @return object Model: UserProvider 更新的用户
     */
    public function checkUpdate($user_provider, $socialUser)
    {
        $newData = collect([
            'name' => empty($socialUser->getName()) ? $socialUser->getNickname() : $socialUser->getName(),
            'avatar' => $socialUser->getAvatar(),
        ]);

        $oldData = [
            'name' => $user_provider->name,
            'avatar' => $user_provider->avatar
        ];

        // 比较并获取要更新的数据
        $diff = $newData->diff($oldData)->toArray();

        if (! empty($diff))
            UserProvider::whereUserId($user_provider->user_id)->update($diff);

        return model('User')->whereId($user_provider->user_id)->first();
    }
}