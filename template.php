<?php
1class template {

    private $file;
    
    /**
     * setTemplate - sets the template file
     *
     * @param  string $file
     *
     */
    public function setTemplate($file) {
        $file = dirname(dirname(__DIR__))."/".$file;
        if(!file_exists($file)) { die("Unable to find template file: ". $file); }
        $this->file = file_get_contents($file); 
    }

    /**
     * setLoop - Loops through an string replacing it for every array item
     *
     * @param  string $tag
     * @param  string $array
     */
    public function setLoop($tag, $array) {
        $toReplace = '';
        preg_match("/{{$tag}}(.*?){\/{$tag}}/s", $this->file, $toLoop); 

        if(!empty($this->file)) {
            if(isset($toLoop[1])) {
                foreach($array as $item) {
                    $newItem = $toLoop[1];
                    foreach($item as $key=>$value) {
                        $newItem = str_replace("[!$key]", $value, $newItem);
                    }
                    $toReplace .= $newItem;
                }
                $this->file = preg_replace("/{{$tag}}(.*?){\/{$tag}}/s", $toReplace, $this->file);
            }
        }
    }


    /**
     * setIf - sets an if on an tag if $condition is true, it will show the text between the tag
     *
     * @param  void $tag
     * @param  string $condition
     */
    public function setIf($tag, $condition) {
        preg_match("/{if-{$tag}}(.*?){\/if-{$tag}}/s", $this->file, $toLoop); 

        if(!empty($this->file)) {
            if(isset($toLoop[1])) {
                if($condition) {
                    $this->file = str_replace("{if-$tag}", "", $this->file);
                    $this->file = str_replace("{/if-$tag}", "", $this->file);
                }else{ 
                    $this->file = str_replace($toLoop[0], "", $this->file);
                }
            }
        }
    }

    /**
     * setText - replaces $tag with $value on $this->file
     *
     * @param  string $tag
     * @param  string $value
     */
    public function setText($tag, $value) { 
        if(!empty($this->file)) {
            $this->file = str_replace("[@$tag]", $value, $this->file);
        }
    }

    /**
     * render - returns the $this->file string
     *
     * @return string $this->file
     */
    public function render() {
        if(!empty($this->file)) {
            return $this->file;
        }else{ die("Template not set"); }
    }
    
}
