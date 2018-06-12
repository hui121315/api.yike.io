<?php

namespace App\Providers;

use App\Comment;
use App\Observers\CommentObserver;
use App\Validators\HashValidator;
use App\Validators\IdNumberValidator;
use App\Validators\KeepWordValidator;
use App\Validators\PhoneValidator;
use App\Validators\PhoneVerifyCodeValidator;
use App\Validators\PolyExistsValidator;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Overtrue\EasySms\EasySms;

class AppServiceProvider extends ServiceProvider
{
    protected $validators = [
        'poly_exists' => PolyExistsValidator::class,
        'phone' => PhoneValidator::class,
        'id_no' => IdNumberValidator::class,
        'verify_code' => PhoneVerifyCodeValidator::class,
        'keep_word' => KeepWordValidator::class,
        'hash' => HashValidator::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Resource::withoutWrapping();
        Carbon::setLocale('zh');

        Comment::observe(CommentObserver::class);

        $this->registerValidators();
    }

    /**
     * Register validators.
     */
    protected function registerValidators()
    {
        foreach ($this->validators as $rule => $validator) {
            Validator::extend($rule, "{$validator}@validate");
        }
    }

    protected function registerSmsService()
    {
        $this->app->singleton(EasySms::class, function () {
            return new EasySms(config('sms'));
        });

        $this->app->alias(EasySms::class, 'sms');
    }
}
