<?php

class RoutePart
{
    public $name;
    public $isParam;
    public $regexp;

    function __construct(string $name, bool $param = false, string $regexp = "")
    {
        $this->name = $name;
        $this->isParam = $param;
        $this->regexp = $regexp;
    }
}

class Route
{
    private $size = 0;
    /** @var RoutePart[] $parts */
    private $parts = [];
    public $callback;

    public function __construct(string $url, $callback, ...$regexp)
    {
        $exp = explode('/', $url);
        $re = 0;
        $resize = count($regexp);
        foreach ($exp as $part) {
            $size = strlen($part);
            // Empty delimeter (/user//profile), skip it
            if ($size == 0)
                continue;
            // Parameter (/user/:id/profile)
            if (substr($part, 0, 1) == ':') {
                if ($size == 1)
                    throw new Exception('No name given for parameter');
                $name = substr($part, 1, $size - 1);
                // Duplicate parameter check (/user/:id/values/:id/)
                foreach ($this->parts as $part) {
                    /** @var RoutePart $part */
                    if ($part->name == $name && $part->isParam)
                        throw new Exception('Duplicate parameter name');
                }
                if ($re < $resize)
                    array_push($this->parts, new RoutePart($name, true, $regexp[$re++]));
                else
                    array_push($this->parts, new RoutePart($name, true));
            } else {
                // Basic part
                array_push($this->parts, new RoutePart($part));
            }
        }
        $this->size = count($this->parts);
        $this->callback = $callback;
    }

    public function match(string $req)
    {
        $expraw = explode('/', $req);
        $exp = [];
        for ($i = 0; $i < count($expraw); $i++)
            if (strlen($expraw[$i]) == 0)
                continue;
            else
                array_push($exp, $expraw[$i]);

        $reqsize = count($exp);
        if ($reqsize != $this->size)
            return false;
        $args = [];
        for ($i = 0; $i < $reqsize; $i++) {
            $part = $exp[$i];
            $rp = $this->parts[$i];
            if ($rp->isParam) {
                if (empty($rp->regexp))
                    array_push($args, $part);
                else {
                    $match = preg_match($rp->regexp, $part);
                    if ($match === false || $match == 0)
                        return false;
                    array_push($args, $part);
                }
                continue;
            }
            if ($rp->name != $part)
                return false;
        }
        return $args;
    }
}
