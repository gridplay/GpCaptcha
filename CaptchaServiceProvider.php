<?php
namespace GridPlay\GpCaptcha;

use Illuminate\Support\ServiceProvider;

class CaptchaServiceProvider extends ServiceProvider
{
    protected $defer = false;
    public function register() {
    }
    public function provides() {
        return ['gpcaptcha'];
    }
    public function boot() {
        //
    }
    protected function packagePath($path = '') {
        return sprintf('%s/../%s', __DIR__, $path);
    }
}
