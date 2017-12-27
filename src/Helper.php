<?php

namespace yii2lab\helpers;

use Yii;
use yii2lab\helpers\yii\FileHelper;

class Helper {
	
	static function getBundlePath($path) {
		if(empty($path)) {
			return false;
		}
		$alias = FileHelper::normalizeAlias($path);
		$dir = Yii::getAlias($alias);
		if(!is_dir($dir)) {
			return false;
		}
		return $alias;
	}
	
	static function getCurrentDbDriver() {
		$dsn = config("components.db.dsn");
		$driver = explode(':', $dsn)[0];
		return  $driver;
	}
	
	static function getClassName($className, $namespace) {
		if(empty($namespace)) {
			return $className;
		}
		if(! Helper::isClass($className)) {
			$className = $namespace . '\\' . ucfirst($className);
		}
		return $className;
	}
	
	static function isAlias($name) {
		return $name[0] == '@';
	}
	
	static function getPath($name) {
		if(self::isAlias($name)) {
			$name = str_replace('\\', '/', $name);
			return Yii::getAlias($name);
		} else {
			return ROOT_DIR . DS . $name;
		}
	}
	
	public static function getNamespace($name) {
		$name = trim($name, '\\');
		$arr = explode('\\', $name);
		array_pop($arr);
		$name = implode('\\', $arr);
		return $name;
	}
	
	static function normalizeComponentConfig($config, $class = null) {
		if(empty($config) && empty($class)) {
			return null;
		}
		if(!empty($class)) {
			$config['class'] = $class;
		}
		if(is_array($config)) {
			return $config;
		}
		$resultConfig = [];
		if(self::isClass($config)) {
			$resultConfig['class'] = $config;
		}
		return $resultConfig;
	}
	
	static function isClass($name) {
		return is_string($name) && strpos($name, '\\') !== false;
	}
	
	static function getDbConfig($name = null, $isEnvTest = YII_ENV_TEST)
	{
		$configName = $isEnvTest ? 'test' : 'main';
		$config = env("db.$configName");
		if($name) {
			return $config[$name];
		} else {
			return $config;
		}
	}
	
	static function generateRandomString($length = 8,$set=null,$set_characters=null,$hight_quality=false) {
		if(empty($set) && empty($set_characters)) {
			$set = 'num|lower|upper';
		}
		$characters = '';
		$arr = explode('|',$set);
		if(in_array('num',$arr)) {
			$characters .= '0123456789';
		}
		if(in_array('lower',$arr)) {
			$characters .= 'abcdefghijklmnopqrstuvwxyz';
		}
		if(in_array('upper',$arr)) {
			$characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		if(in_array('char',$arr)) {
			$characters .= '~!@#$^&*`_-=*/+%!?.,:;\'"\\|{}[]<>()';
		}
		if(!empty($set_characters)) {
			$characters .= $set_characters;
		}
		$randstring = '';
		if($hight_quality) {
			$char_arr = array();
			$characters_len = mb_strlen($characters,'utf-8');
		}
		for($i = 0; $i < $length; $i++) {
			$r = mt_rand(0,strlen($characters)-1);
			if($hight_quality) {
				if(in_array($r,$char_arr)) {
					while(in_array($r,$char_arr)) {
						$r = mt_rand(0,strlen($characters)-1);
					}
				}
				$char_arr[] = $r;
				if(count($char_arr) >= $characters_len) {
					$char_arr = array();
				}
			}
			$randstring .= $characters[$r];
		}
		return $randstring;
	}
	
	/*static function strToArray($value) {
		$value = trim($value, '{}');
		$value = explode(',', $value);
		return $value;
	}*/
	
	static function timeForApi($value, $default = null, $mask = 'Y-m-d\TH:i:s\Z') {
		if(APP != API) {
			return $value;
		}
		if(empty($value)) {
			return $default;
		}
		if(is_numeric($value)) {
			$value = date('Y-m-d H:i:s', $value);
		}
		$datetime = new \DateTime($value);
		$value = $datetime->format($mask);
		return $value;
	}
	
	/**
	 * @return array
	 *
	 * @deprecated moved to common\enums\app\AppEnum::values()
	 */
	public static function getApps() {
		return [COMMON, FRONTEND, BACKEND, API, CONSOLE];
	}
	
	/*public static function getWebApps() {
		return [COMMON, FRONTEND, BACKEND, API];
	}*/
	
	public static function getApiVersionList()
	{
		$dir = Yii::getAlias('@api');
		$dirList = FileHelper::scanDir($dir);
		$result = [];
		foreach($dirList as $path) {
			if (preg_match('#v([0-9]+)#i', $path)) {
				$result[] = $path;
			}
		}
		return $result;
	}
	
	public static function getApiSubApps()
	{
		$subApps = self::getApiVersionList();
		$result = [];
		foreach($subApps as $app) {
			$result[] = API . '/' . $app;
		}
		return $result;
	}
	
}