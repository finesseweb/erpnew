<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class processStyleAndData {

    protected $divStyle = array();
    protected $data = array();
    protected $classli = 'class="text';
    protected $title = 'Title';
    protected $legend_style = array();
    protected $wrapper_style = array();
    protected $liStyle = array();
    protected $body;

    protected function crunchStyle(array $style) {
        $this->divStyle = $style;
        $this->style = 'style="';
        foreach ($this->divStyle as $key => $value) {
            $this->style .= $key . ':' . $value . '; ';
        }
        return $this->style .= '" ';
    }

    protected function setData(array $data) {
        $this->data = $data;
        $this->body = "<div class='top_container' " . $this->crunchStyle($this->divStyle) . "><label for='triggercheck'><image id='triggercheck' style='position:absolute; top:35%; right:30%; transform:translate(-50%,-50%);' src='themes/exclusive/images/delete.gif' /></label><fieldset " . $this->crunchStyle($this->wrapper_style) . "><table border = 1 class ='top_container--data_container' " . $this->crunchStyle($this->datastyle) . "><legend " . $this->crunchStyle($this->legend_style) . ">" . $this->title . "</legend><thead><tr><th style='color:#ccc;'>#S-No</th><th style='color:#ccc;'>No Of Days Left</th><th style='color:#ccc;'>Statutory Name</th><th style='color:#ccc;'>Name Of Return</th><th style='color:#ccc;'>Frequency</th></tr></thead>";
        foreach ($this->data['days'] as $key => $value){
          if($this->data['days'][$key]<0){
              $color='#ff1a43;';
          }
          else
                $color='yellow;';
            $this->body .= "<tr>";
             $this->body .= '<th ' . $this->classli . $key . '" ' . $this->crunchStyle($this->liStyle) . '  ><span style="color:'.$color.'">' .($key+1) . '</span></th>';
            $this->body .= '<td ' . $this->classli . $key . '" ' . $this->crunchStyle($this->liStyle) . ' style="margin-top:1em; border:0px solid #0000; font-size:1.2em" ><span style="color:'.$color.'">' . $this->data['days'][$key] . '</span></td>';
            $this->body .= '<td ' . $this->classli . $key . '"' . $this->crunchStyle($this->liStyle) . ' style="margin-top:1em; border:0px solid #0000; font-size:1.2em" ><span style="color:'.$color.'">' . $this->data['s_name'][$key] . '</span></td>';
            $this->body .= '<td ' . $this->classli . $key . '" ' . $this->crunchStyle($this->liStyle) . ' style="margin-top:1em; border:0px solid #0000; font-size:1.2em" ><span style="color:'.$color.'">' . $this->data['r_name'][$key] . '</span></td>';
            $this->body .= '<td ' . $this->classli . $key . '" ' . $this->crunchStyle($this->liStyle) . ' style="margin-top:1em; border:0px solid #0000; font-size:1.2em" ><span style="color:'.$color.'">' . $this->data['f_name'][$key] . '</span></td>';
            $this->body .= "</tr>";
        }
        return $this->body .= '</table></fieldset></div>';
    }

}

class coverDiv extends processStyleAndData {

    protected function applyCover() {
        $this->body = $this->setData($this->data);
        //  echo "<pre>"; print_r($this->data);
        return $this->body;
    }

    protected function scriptClose() {
        $js = '<script>';
        $js .= '$("#triggercheck").click(function(){$(".top_container").hide();$("body").css("overflow","visible");});';
        $js .= '$("body").css("overflow","hidden");';
        $js .= '</script>';
        return $js;
    }

}
