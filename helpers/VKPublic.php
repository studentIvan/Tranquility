<?php
/**
 * Vkontakte.Ru groups management helper
 */
class VKPublic
{
    protected $groupId, $appId, $secretKey, $accessToken, $accessSecret;

    /**
     * @param int $groupId
     * @param int $appId
     * @param string $secretKey
     */
    public function __construct($groupId, $appId, $secretKey)
    {
        $this->groupId = $groupId;
        $this->appId = $appId;
        $this->secretKey = $secretKey;
    }

    /**
     * @param string $accessToken
     * @param string $accessSecret
     */
    public function setAccessData($accessToken, $accessSecret)
    {
        $this->accessToken = $accessToken;
        $this->accessSecret = $accessSecret;
    }

    /**
     * Hack
     */
    public function getAccessData()
    {
        echo "<!doctype html><html><head><meta charset='utf-8'></head>
            <body><a href='http://api.vkontakte.ru/oauth/authorize?" .
            "client_id={$this->appId}&scope=offline,wall,groups,pages," .
            "photos,docs,audio,video,notes,stats,messages,notify,notifications,nohttps&amp;" .
            "redirect_uri=http://api.vkontakte.ru/blank.html&amp;response_type=code'
                target='_blank'>Получить CODE</a><br>Ссылка для получения токена:<br>
                <b>https://api.vkontakte.ru/oauth/access_token?client_id={$this->appId}" .
            "&amp;client_secret={$this->secretKey}&amp;code=CODE</b></body></html>";

        exit;
    }

    /**
     * @param string $method
     * @param mixed $parameters
     * @return mixed
     */
    public function callMethod($method, $parameters)
    {
        if (!$this->accessToken) return false;
        if (is_array($parameters)) $parameters = http_build_query($parameters);
        $queryString = "/method/$method?$parameters&access_token={$this->accessToken}";
        $querySig = md5($queryString . $this->accessSecret);
        return json_decode(file_get_contents(
            "http://api.vk.com{$queryString}&sig=$querySig"
        ));
    }

    /**
     * @param string $message
     * @param bool $fromGroup
     * @param bool $signed
     * @return mixed
     */
    public function wallPostMsg($message, $fromGroup = true, $signed = false)
    {
        return $this->callMethod('wall.post', array(
            'owner_id' => -1 * $this->groupId,
            'message' => $message,
            'from_group' => $fromGroup ? 1 : 0,
            'signed' => $signed ? 1 : 0,
        ));
    }

    /**
     * @param string $attachment
     * @param null|string $message
     * @param bool $fromGroup
     * @param bool $signed
     * @return mixed
     */
    public function wallPostAttachment($attachment, $message = null, $fromGroup = true, $signed = false)
    {
        return $this->callMethod('wall.post', array(
            'owner_id' => -1 * $this->groupId,
            'attachment' => strval($attachment),
            'message' => $message,
            'from_group' => $fromGroup ? 1 : 0,
            'signed' => $signed ? 1 : 0,
        ));
    }

    /**
     * @param string $file relative file path
     * @return mixed
     */
    public function createPhotoAttachment($file)
    {
        $result = $this->callMethod('photos.getWallUploadServer', array(
            'gid' => $this->groupId
        ));

        $ch = curl_init($result->response->upload_url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'photo' => '@' . getcwd() . '/' . $file
        ));

        if (($upload = curl_exec($ch)) === false) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);
        $upload = json_decode($upload);
        $result = $this->callMethod('photos.saveWallPhoto', array(
            'server' => $upload->server,
            'photo' => $upload->photo,
            'hash' => $upload->hash,
            'gid' => $this->groupId,
        ));

        return $result->response[0]->id;
    }

    public function combineAttachments()
    {
        $result = '';
        if (func_num_args() == 0) return '';
        foreach (func_get_args() as $arg) {
            $result .= strval($arg) . ',';
        }
        return substr($result, 0, strlen($result) - 1);
    }
}