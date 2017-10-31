<?php

namespace AppBundle\Helper;

class UrlCheckerHelper
{
    /**
     * Checks if the given url is a valid url.
     * Source: http://www.php.net/manual/en/function.preg-match.php#93824
     *
     * @param string $url
     * @return bool
     */
    public function isUrlValid($url) : bool
    {
        $regex = "((https?|ftp)\:\/\/)?"; // SCHEME
        $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
        $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
        $regex .= "(\:[0-9]{2,5})?"; // Port
        $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
        $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
        $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor

        return preg_match("/^$regex$/", $url);
    }
}