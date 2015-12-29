<?php

class Captcha {

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // :: Private Properties
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

	/**
	 * Configs.
	 */
	private static $_img = [
		"width"  => 180,
		"height" => 60,
		"chars"  => 6
	];

	/**
	 * Configs.
	 */
	private static $_cfg = [
		"chars" => "abcdefghjkmnopqrstuvwqyz234567890",
		"dots"  => 40,
		"lines" => 20,
		"font"  =>"/monaco.ttf",
		"color" =>  [
			"text"       => "0xA9A9A9",
			"noise"      => "0xA9A9A9",
			"background" => [
				"R" => 0x21,
				"G" => 0x21,
				"B" => 0x21
			]
		]
	];

	/**
	 * Image color variables.
	 * @var [array]
	 */
	private static $_color = [
		"bg"    => NULL,
		"text"  => NULL,
		"noise" => NULL
	];

	/**
	 * Random generated code.
	 * @var [strig]
	 */
	private static $_code;

	/**
	 * PHP GD Generated image.
	 */
	private static $_image;
	
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // :: Public Methods
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    
    /**
     * Generate random captcha image.
     */
	public static function make() {
        
        if (session_status() !== 2) {
            throw new \Exception('PHP Session not started.');
        }

        self::$_cfg["font"] = self::_getClassDir() . self::$_cfg["font"];
        
		self::_makeCode();
		self::_makeImage();
		self::_generateColors();
		self::_generateDots();
		self::_generateLines();
		self::_createTextBox();
		self::_showImage();
	}

	/**
	 * Check last generated captcha code.
	 * @param [string] $code
	 */
	public static function check($code) {
		return !(!isset($_SESSION['captcha_code']) || strcasecmp($_SESSION['captcha_code'], $code) != 0);
	}

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // :: Private Methods
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    
    /**
     * Generate random code.
     */
	private static function _makeCode() {
		$code = ''; $chars = self::$_cfg['chars'];

		for ($i = 0; $i < self::$_img['chars']; $i++) {
			$code .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}

		self::$_code = $code;
	}

	/**
	 * Generate image colors.
	 */
	private static function _makeImage() {
		self::$_image = imagecreate(self::$_img["width"], self::$_img["height"]);
	}

	private static function _generateColors() {
		$r = self::$_cfg["color"]["background"]["R"];
		$g = self::$_cfg["color"]["background"]["G"];
		$b = self::$_cfg["color"]["background"]["B"];

		// text color array
		$tca = self::_hexrgb(self::$_cfg["color"]["text"]);

		// noise color array
		$nca = self::_hexrgb(self::$_cfg["color"]["text"]);

		self::$_color["bg"]    = imagecolorallocate(self::$_image, $r, $g, $b);
		self::$_color["text"]  = imagecolorallocate(self::$_image, $tca['R'],$tca['G'], $tca['B']);
		self::$_color["noise"] = imagecolorallocate(self::$_image, $nca['R'],$nca['G'], $nca['B']);
	}
	
	/**
	 * Generate random dots on image.
	 */
	private static function _generateDots() {

		for ($i = 0; $i < self::$_cfg["dots"]; $i++) {
			$cx = mt_rand(0, self::$_img["width"]);
			$cy = mt_rand(0, self::$_img["height"]);

			imagefilledellipse(self::$_image, $cx, $cy, 2, 3, self::$_color["noise"]);
		}
	}

	/**
	 * Generate random lines on image.
	 */
	private static function _generateLines() {
		for($i = 0; $i < self::$_cfg["lines"]; $i++) {
			$x1 = mt_rand(0, self::$_img["width"]);
			$y1 = mt_rand(0, self::$_img["height"]);
			$x2 = mt_rand(0, self::$_img["width"]);
			$y2 = mt_rand(0, self::$_img["height"]);

			imageline(self::$_image, $x1, $y1, $x2, $y2, self::$_color["noise"]);
		}
	}

	/**
	 * Create image box.
	 */
	private static function _createTextBox() {
		$fontSize = self::$_img["height"] * 0.50;
		$textbox  = imagettfbbox($fontSize, 0, self::$_cfg["font"], self::$_code);

		$x = (self::$_img["width"]  - $textbox[4]) / 2;
		$y = (self::$_img["height"] - $textbox[5]) / 2;

		imagettftext(self::$_image, $fontSize, 0, $x, $y, self::$_color["text"], self::$_cfg["font"], self::$_code);
	}

	/**
	 * Render image on screen and save generated code in PHP session variable.
	 */
	private static function _showImage() {
		header('Content-Type: image/jpeg');
		imagejpeg(self::$_image);
		imagedestroy(self::$_image);
        
        $_SESSION['captcha_code'] = self::$_code;
	}

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // :: Helper Methods
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    
    /**
     * Find absolute dir location for this file.
     * @return [string]
     */
	private static function _getClassDir() {
		$autoloader_reflector = new ReflectionClass("Captcha");
		$class_file_nanme = $autoloader_reflector->getFileName();
		return dirname($class_file_nanme);
	}

	/**
	 * Convert HAX string into RGB color array.
	 * @param  [string] $hexstr
	 * @return [array]
	 */
	private static function _hexrgb($hexstr) {
		$int = hexdec($hexstr);

		return [
			"R" => 0xFF & ($int >> 0x10),
			"G" => 0xFF & ($int >> 0x8),
			"B" => 0xFF & ($int)
		];
	}
}
