<?php
function create_subdomain($subDomain,$cPanelUser,$cPanelPass,$rootDomain) {
 
    $buildRequest = "/frontend/paper_lantern/subdomain/doadddomain.html?rootdomain=" . $rootDomain . "&domain=" . $subDomain . "&dir=subdomains/" . $subDomain;
 
    $openSocket = fsockopen('localhost',2082);
    if(!$openSocket) {
        return "Socket error";
        exit();
    }
 
    $authString = $cPanelUser . ":" . $cPanelPass;
    $authPass = base64_encode($authString);
    $buildHeaders  = "GET " . $buildRequest ."\r\n";
    $buildHeaders .= "HTTP/1.0\r\n";
    $buildHeaders .= "Host:localhost\r\n";
    $buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
    $buildHeaders .= "\r\n";
 
    fputs($openSocket, $buildHeaders);
    
    while(!feof($openSocket)) {
        fgets($openSocket,128);
    }
    
    function recursiveCopy($source, $destination)
    {
        if (!file_exists($destination)) {
            mkdir($destination);
        }
    
        $splFileInfoArr = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
    
        foreach ($splFileInfoArr as $fullPath => $splFileinfo) {
            //skip . ..
            if (in_array($splFileinfo->getBasename(), [".", ".."])) {
                continue;
            }
            //get relative path of source file or folder
            $path = str_replace($source, "", $splFileinfo->getPathname());
    
            if ($splFileinfo->isDir()) {
                mkdir($destination . "/" . $path);
            } else {
            copy($fullPath, $destination . "/" . $path);
            }
        }
    }
    
    $source = "/home/propertysalers/public_html/design";
    $destination = "/home/propertysalers/public_html/subdomains/".$subDomain;
    
    recursiveCopy($source,$destination);
    
    fclose($openSocket);
 
    $newDomain = "http://" . $subDomain . "." . $rootDomain . "/";
 
 return "Created subdomain <a href=".$newDomain." >".$newDomain."</a>";
 
}




$domain = "hassan";
$cPanelUser = "propertysalers";
$cpanelPass = "66iXq2Jqz8";
$rootDomain = "propertysalers.com";

echo create_subdomain($domain,$cPanelUser,$cpanelPass,$rootDomain);



?>