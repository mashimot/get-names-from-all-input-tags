<?php

class Attribute {
    private $url = null;
    private $dom = null;

    public function __construct(){
        $this->dom = new \DOMDocument();
    }
    public function doesURLExist(){
        return @$this->dom->loadHTMLFile($this->url);
    }
    public function getAttribute(){
        $tags = array();

        $json = new stdClass;
        $json->success = false;
        $json->url = $this->url;
        $json->message = "";
        $json->tags = $tags;

        if($this->doesURLExist()) {
            $elements = $this->dom->getElementsByTagName("*");
            foreach ($elements as $k => $e) {
                $i = 0;
                $isFinded = false;
                $tagLength = count($tags);
                $name = $e->getAttribute('name');
                if (trim($name) !== '') {
                    $input = new StdClass;
                    $input->type = $e->tagName;
                    $input->id = $e->getAttribute('id');
                    $input->value = $e->getAttribute('value');
                    $input->line = $e->getLineNo();

                    while ($i < $tagLength && !$isFinded) {
                        if ($tags[$i]->name == $name) {
                            $tags[$i]->input[] = $input;
                            $isFinded = true;
                        }
                        $i++;
                    }
                    if (!$isFinded) {
                        $tags[] = new stdClass();
                        $lastIndex = count($tags) - 1;
                        $tags[$lastIndex]->name = $name;
                        $tags[$lastIndex]->input[] = $input;
                    }
                }
            }
            $json->success = true;
            $json->message = "Tags successfully created!";
            $json->tags = $tags;
        } else {
            $json->success = false;
            $json->message = "URL does not exist!";
        }
        return $json;
    }

    public function setURL($url){
        $this->url = $url;
    }
}
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

if(isset($_GET['url'])){
    $attr = new Attribute();
    $attr->setURL($_GET['url']);
    echo json_encode($attr->getAttribute());
}
