<?php
namespace GridPlay\GpCaptcha;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Cache;
use Illuminate\Support\Request;
class GpCaptcha
{
	private static function senddata($meth = 'get', $uri = '', $data = [], $h = [])
	{
		$h['content-type'] = 'application/json';
		$http = Http::withHeaders($h);
		$url = "https://captcha.gridplay.ca/api/".$uri;
		try {
			if (strtolower($meth) == "get") {
				$response = $http->get($url);
			}
			if (strtolower($meth) == "post") {
				$response = $http->post($url, $data);
			}
			dd($response);
			if ($response->ok()) {
				return $response->json();
			}else{
				return ['error' => 'not found'];
			}
		}catch(\Exception $e) {
			return ['error' => 'Connection invalid'];
		}
		return ["error" => 'Unable to connect'];
	}
	public static function Get($class = '')
	{
		$ret = self::senddata('get', 'captcha', [], []);
		$img = "";
		if (is_array($ret) && !array_key_exists('error', $ret)) {
			$token = $ret['token'];
			$timestamp = $ret['timestamp'];
			Cache::put("captcha:$token", $timestamp, now()->addMinutes(5));
			$img = '<img src="'.$ret['image'].'"><br>';
			$img .= '<input type="hidden" name="token" value="'.$token.'">';
			$img .= '<input type="text" name="captcha" value="" class="'.$class.'">';
		}else{
			dd($ret);
		}
		return $img;
	}
	public static function Validate(Request $request)
	{
		$timestamp = Cache::pull("captcha:{$request->token}");
		$data = [
			'token' => $request->token,
			'timestamp' => $timestamp,
			'input' => $request->captcha
		];
		$ret = self::senddata('post', 'captcha/verify', $data, []);
		if (is_array($ret)) {
			if ($ret['success'] === true) {
				return true;
			}
		}
		return false;
	}
}
