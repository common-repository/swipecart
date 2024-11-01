<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Icons SVG
 * 
 * @package Swipecart
 * @author Manthan Kanani
 * @since 2.1.1
**/
class SC_SVGIcons{
    
    /**
     * get Icon if have
     *
     * @package Swipecart
     * @author Manthan Kanani
     * @since 2.1.1
    **/
    public function getIcon($icon){
        $icons = $this->getIcons();

        if(isset($icons[$icon])){
            return $icons[$icon];
        } 
        return false;
    }

    /**
     * Convert SVG Icon to Base64 Decoded Image to set it as Icon
     *
     * @package Swipecart
     * @author Manthan Kanani
     * @since 2.1.2
    **/
    public function getEncodedSVGIcon($key){
        $base_encode = 'data:image/svg+xml;base64,'.base64_encode($this->getIcon($key));
        return $base_encode;
    }

    /**
     * Create Icon from SVG
     *
     * @package Swipecart
     * @author Manthan Kanani
     * @since 2.1.2
    **/
    public function getIcons(){
        return array(
            "rentech-digital-r"             => '<svg xmlns="http://www.w3.org/2000/svg" width="780" height="780" viewBox="0 0 780 780" fill="none"><path d="M386.9 440H261.5L262 440.8L340.8 564.5L344.3 570L478 780H390C174.6 780 0 605.4 0 390V260C0 260 184 260 186.5 260C189 260 187.5 260 189.2 260C190.9 260 301 260 301 260C330.68 260.306 360.371 260.1 390 260.1C393.8 260.1 397.6 260.3 401.3 260.8C445.7 266.4 480 304.2 480 350.1C480 391.6 451.9 426.6 413.6 437C407 438.8 400.1 439.9 393.1 440.1C392.1 440.1 391 440.2 390 440.2C389 440.2 387.9 440 386.9 440Z"/><path d="M780 390V780H632L498.4 570L484.8 548.6C531.5 526.3 569.1 488 590.5 440.8C590.6 440.5 590.7 440.3 590.8 440C603.1 412.5 610 382.1 610 350C610 318.2 603.3 288 591.1 260.8C591 260.6 590.9 260.3 590.8 260C556.4 183.4 479.4 130 390 130H0V0H390C605.4 0 780 174.6 780 390Z"/></svg>',
            "swipecart-o"                   => '<svg width="774" height="776" viewBox="0 0 774 776" fill="none" xmlns="http://www.w3.org/2000/svg"><path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M386.7 775.499C600 775.499 772.9 602.376 772.9 388.8C772.9 175.124 600 2 386.7 2C173.4 2 0.399902 175.124 0.399902 388.7C0.399902 602.376 173.4 775.499 386.7 775.499ZM383.2 576.242C484.7 576.242 567 493.835 567 392.204C567 290.573 484.7 208.166 383.2 208.166C281.7 208.166 199.4 290.573 199.4 392.204C199.4 493.835 281.7 576.242 383.2 576.242Z" fill="white"/><path opacity="0.3" d="M226.92 296C226.92 296.1 226.82 296.1 226.82 296.2C210.421 315.204 187.123 384.819 209.421 451.734C252.718 581.661 452.901 651.876 620.687 525.149C687.482 474.538 750.276 401.423 771.174 361.514C785.173 485.741 715.58 605.367 666.884 653.177C602.589 722.192 528.995 749.998 490.298 761C401.206 759.1 327.511 723.092 301.814 706.488C209.821 655.277 177.8 557.91 173.5 512.5C160 397 200.322 326.807 226.82 296.2C226.82 296.1 226.82 296.1 226.92 296Z" fill="white"/><path opacity="0.6" d="M129 322.915C232 229.294 310 214.499 373 207.266C107 237.805 58.5 743.458 497.2 759.379C270.7 827.066 58.2999 676.772 13.8999 488.128C32 416.035 98.2 352.453 129 322.915Z" fill="white"/><path fill-rule="evenodd" clip-rule="evenodd" d="M457.088 222.375C288.588 148.88 40.0881 363.958 14.8881 488.318C-42.5119 272.64 90.9881 113.234 178.688 62.8687C318.188 -28.8498 470.288 -0.212796 540.188 32.3292C580.788 46.3474 609.988 85.2977 609.988 131.057C609.988 188.631 563.888 235.392 506.988 235.392C488.888 235.392 471.888 230.686 457.088 222.375Z" fill="white"/></svg>'
        );
    }

}