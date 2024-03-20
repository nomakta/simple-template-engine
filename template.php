<?php

use InvalidArgumentException;
use RuntimeException;
class template {

    private $file;
    
    /**
     * Sets the template file to be used for rendering.
     *
     * This method loads the template file specified by the file name, sets it as the
     * template for rendering, and stores its contents in the object property.
     * If the specified file does not exist, the method terminates with an error message.
     *
     * @param string $file The name of the template file to be loaded.
     * @throws InvalidArgumentException If the template file is not found.
     * @throws RuntimeException If unable to read the template file.
     * @return void
    */
    public function setTemplate(string $file): void {
        $filePath = dirname(dirname(__DIR__))."/app/views/".$file;

        // Validate file path to prevent directory traversal
        if (!realpath($filePath) || strpos(realpath($filePath), realpath(dirname(dirname(__DIR__)) . "/app/views")) !== 0) {
            throw new InvalidArgumentException("Invalid template file path: " . $filePath);
        }
        // Check if the template file exists
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("Template file not found: " . $filePath);
        }
    
        // Load the contents of the template file
        $contents = file_get_contents($filePath);
        
        if ($contents === false) {
            throw new RuntimeException("Unable to read template file: " . $filePath);
        }
    
        // Set the template contents
        $this->file = $contents;
    }

    /**
     * Replaces a loop block in the template file with content generated from an array.
     *
     * This method searches for a loop block in the template file delimited by
     * {tag} and {/tag}, replaces it with content generated from the provided array,
     * and updates the template accordingly. If the provided array is not an array,
     * an error message is displayed instead.
     *
     * @param string $tag    The tag used to identify the loop block in the template.
     * @param array  $array  The array containing data for generating loop content.
     * @param string $error  The error message to display if the provided array is not an array.
     *                       Default is "Unable to set loop".
     * @return void
    */
    public function setLoop(string $tag, array $array, string $error = "Unable to set loop"): void {
        $toReplace = '';
        preg_match("/{{$tag}}(.*?){\/{$tag}}/s", $this->file, $toLoop); 

        if (empty($toLoop)) {
            return; // No loop block found, early return
        }

        $loopContent = $toLoop[1];

        if (!is_array($array)) {
            $this->file = preg_replace("/{{$tag}}(.*?){\/{$tag}}/s", '<p class="text-white">' . $error . '</p>', $this->file); 
            return; // Provided array is not an array, early return
        }

        foreach ($array as $item) {
            $newItem = $loopContent;
            foreach ($item as $key => $value) {
                $newItem = preg_replace("/\[!$key\]/", $value, $newItem);
            }
            $toReplace .= $newItem;
        }

        $this->file = preg_replace("/{{$tag}}(.*?){\/{$tag}}/s", $toReplace, $this->file);
    }


    /**
     * Sets conditional blocks in the template file based on the provided condition.
     *
     * This method searches for conditional blocks in the template file delimited by
     * {if-tag} and {/if-tag} or {else-tag} and {/else-tag} for the specified tag, and
     * replaces them based on the provided condition. If the condition is true, the
     * content inside {if-tag} and {/if-tag} is retained, and the content inside {else-tag}
     * and {/else-tag} (if exists) is removed. If the condition is false, the content inside
     * {if-tag} and {else-tag} is removed, while the content inside {else-tag} and {/else-tag}
     * (if exists) is retained.
     *
     * @param string $tag       The tag used to identify the conditional blocks in the template.
     * @param bool   $condition The condition determining whether to retain or remove the blocks.
     * @return void
    */
    public function setIf(string $tag, bool $condition): void {
        // Extract the contents between {if-tag} and {/if-tag}
        preg_match_all("/{if-{$tag}}(.*?){\/if-{$tag}}/s", $this->file, $toLoop); 

        // If the file is empty or the condition doesn't match any template, return immediately
        if(empty($this->file) || !isset($toLoop[1])) {
            return;
        }
        var_dump($toLoop);
        // If the condition is TRUE , remove the {if-tag} and {/if-tag} + blocks
        // If the condition is FALSE, remove the {else-tag} and {/else-tag} + blocks
        if($condition) {
            // Remove the {else-tag} blocks if they exist
            $this->file = preg_replace("/{else-$tag}(.*?){\/else-$tag}/s", "", $this->file);
            
            $this->file = preg_replace("/\{if-$tag\}/", "", $this->file);
            $this->file = preg_replace("/\{\/if-$tag\}/", "", $this->file);
        } else {
            // If the condition is false, remove the content inside {if-tag} and {else-tag} blocks
            $this->file = preg_replace("/{if-$tag}(.*?){\/if-$tag}/s", "", $this->file);

            $this->file = preg_replace("/\{else-$tag\}/", "", $this->file);
            $this->file = preg_replace("/\{\/else-$tag\}/", "", $this->file);
        }
        
    }
    
    /**
     * Sets the value for a specific tag in the template file.
     *
     * This method searches for a specific tag in the template file delimited by
     * [@tag], replaces it with the provided value, and updates the template accordingly.
     *
     * @param string $tag    The tag whose value needs to be set.
     * @param string $value  The value to be set for the tag.
     * @return void
    */
    public function setText(string $tag, string $value): void {
        // Check if the file content is empty
        if (empty($this->file)) {
            return;
        }

        // Validate input parameters
        if (empty($tag)) {
            throw new InvalidArgumentException("Tag cannot be empty");
        }

        // Perform the replacement
         $this->file = preg_replace("/\[@$tag\]/", $value, $this->file);
    }

    /**
     * Renders the template content.
     *
     * @return string The rendered template content.
    */
    public function render(): string {
        if (empty($this->file)) {
            throw new RuntimeException("Template not set. Please use setTemplate() to set the template or check if the template file is not empty.");
        }
    
        return $this->file;
    }

}