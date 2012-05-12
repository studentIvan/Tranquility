<?php
/**
 * Google ReCaptcha helper
 */
class ReCaptcha
{
    /**
     * Verify ReCaptcha Request
     *
     * @param $privateKey
     * @return bool
     */
    public static function verify($privateKey)
    {
        if (!function_exists('recaptcha_check_answer'))
            include_once dirname(__FILE__) . '/../vendor/ReCaptcha/recaptchalib.php';
        $responseField = Data::input('recaptcha_response_field');
        $challengeField = Data::input('recaptcha_challenge_field');
        if ($responseField) {
            $reCaptchaResponse = recaptcha_check_answer(
                $privateKey, $_SERVER['REMOTE_ADDR'], $challengeField, $responseField);
            $answer = $reCaptchaResponse->is_valid;
            unset($reCaptchaResponse);
            return $answer;
        } else {
            return false;
        }
    }
}
