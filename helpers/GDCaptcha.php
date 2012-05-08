<?php
class GDCaptcha
{
    /**
     * @static
     * @param string $input
     * @return bool
     */
    public static function checkCorrect($input)
    {
        $token = Session::getToken();
        if ($input == Database::getSingleResult("SELECT phrase FROM captcha WHERE token='$token'")) {
            Database::getInstance()
                ->query("DELETE FROM captcha WHERE token='$token'");
            return true;
        } else {
            return false;
        }
    }

    /**
     * @static
     * @param int $length
     * @param int $width
     * @param int $height
     * @param int $lines
     * @param string $font
     */
    public static function show(
        $length = 4, $width = 100, $height = 40, $lines = 4, $font = 'Sansation_Regular.ttf')
    {
        $image = imagecreatetruecolor($width + 2, $height + 2);
        $colours = array(
            array(
                'base' => array(0, 88, 95),
                'text' => array(
                    array(255, 255, 255),
                    array(255, 252, 196),
                    array(240, 237, 187),
                    array(0, 147, 147),
                ),
            ),
            array(
                'base' => array(147, 33, 0),
                'text' => array(
                    array(255, 255, 255),
                    array(255, 252, 196),
                    array(240, 237, 187),
                ),
            ),
            array(
                'base' => array(3, 140, 37),
                'text' => array(
                    array(160, 217, 174),
                    array(3, 203, 44),
                    array(242, 242, 242),
                ),
            ),
            array(
                'base' => array(218, 61, 30),
                'text' => array(
                    array(217, 208, 197),
                    array(200, 156, 148),
                    array(200, 137, 130),
                ),
            ),
            array(
                'base' => array(222, 220, 195),
                'text' => array(
                    array(110, 148, 137),
                    array(65, 67, 74),
                    array(135, 121, 99),
                ),
            ),
            array(
                'base' => array(204, 39, 56),
                'text' => array(
                    array(105, 0, 17),
                    array(105, 191, 178),
                    array(242, 217, 156),
                    array(229, 185, 111),
                ),
            ),
            array(
                'base' => array(140, 132, 104),
                'text' => array(
                    array(71, 40, 44),
                    array(201, 179, 127),
                    array(219, 218, 183),
                    array(196, 196, 156),
                ),
            ),
            array(
                'base' => array(207, 217, 108),
                'text' => array(
                    array(120, 191, 130),
                    array(17, 62, 124),
                    array(48, 88, 64),
                    array(6, 146, 112),
                ),
            ),
            array(
                'base' => array(242, 193, 46),
                'text' => array(
                    array(38, 57, 115),
                    array(25, 73, 115),
                    array(242, 121, 22),
                    array(242, 242, 242),
                ),
            ),
        );

        $scheme = $colours[rand(0, count($colours)-1)];
        $textThemesCount = count($scheme['text'])-1;
        $textThemes = array();
        foreach ($scheme['text'] as $textTheme) {
            $textThemes[] = imagecolorallocate($image, $textTheme[0], $textTheme[1], $textTheme[2]);
        }

        $baseColor = imagecolorallocate($image, $scheme['base'][0], $scheme['base'][1], $scheme['base'][2]);
        imagefill($image, 0, 0, $baseColor);

        $fontSize = floor($height / 4) + floor($height / 10);
        $letters = array_merge(range('A', 'Z'), range(1, 9));
        unset($letters[14], $letters[8], $letters[26]);
        $letters = array_values($letters);
        $lettersCount = count($letters) - 1;
        $xStep = floor($width / $length);
        $yStep = floor($height / 17);
        $yCenter = floor($height / 2) + $yStep;

        $font = dirname(__FILE__) . "/../fonts/$font";

        imagerectangle($image, 0, 0, $width + 1, $height + 1, $textThemes[rand(0, $textThemesCount)]);

        $token = Session::getToken();

        if ($word = Database::getSingleResult(
            "SELECT phrase FROM captcha WHERE token='$token'"))
        {
            $cLetters = str_split($word);
        }
        else
        {
            $word = '';
            for ($i = 0; $i < $length; $i++) {
                $word .= $letters[rand(0, $lettersCount)];
            }

            Database::getInstance()->query(
                "INSERT INTO captcha (token, phrase) VALUES ('$token', '$word')"
            );

            $cLetters = str_split($word);
        }

        /**
         * Place letters
         */
        for ($i = 0; $i < $length; $i++)
        {
            $textColor = $textThemes[rand(0, $textThemesCount)];
            $letter = $cLetters[$i];
            $xTarget = $xStep * $i + floor($xStep / 10);
            imagettftext($image, $fontSize, rand(-10,10),
                $xTarget + (rand(-1, 1) * floor($xStep / 10)), $yCenter + (rand(-2, 2) * $yStep),
                $textColor, $font, $letter);
        }

        /**
         * Place lines
         */
        $style = array(
            $textThemes[rand(0, $textThemesCount)],
            $textThemes[rand(0, $textThemesCount)],
            $textThemes[rand(0, $textThemesCount)],
            $textThemes[rand(0, $textThemesCount)],
            $textThemes[rand(0, $textThemesCount)],
            $textThemes[rand(0, $textThemesCount)],
            $textThemes[rand(0, $textThemesCount)],
            $textThemes[rand(0, $textThemesCount)],
            IMG_COLOR_TRANSPARENT
        );

        imagesetstyle($image, $style);

        foreach(range(1, $lines) as $i) {
            $what = rand(1, 100);
            if ($what > 50) {
                imageline($image, rand(2, $width - 2), rand(2, $height - 2),
                    rand(2, $width - 2), rand(2, $height - 2), IMG_COLOR_STYLED);
            } else {
                imageline($image, rand(2, $width - 2), rand(2, $height - 2),
                    rand(2, $width - 2), rand(2, $height - 2), $textThemes[rand(0, $textThemesCount)]);
            }
        }

        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
        exit;
    }
}
