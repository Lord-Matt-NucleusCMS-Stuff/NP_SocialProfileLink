<?php
/**
 * Description of NP_SocialProfileLink
 *
 * @author lordmatt
 */
class NP_SocialProfileLink extends NucleusPlugin {
    
        private $edition = 6;
    
	// name of plugin
	function getName() {
		return 'Social Profile Link';
	}

	// author of plugin
	function getAuthor() {
		return 'Lord Matt';
	}

	// an URL to the plugin website
	// can also be of the form mailto:foo@bar.com
	function getURL() {
		return 'http://lordmatt.co.uk/';
	}

	// version of the plugin
	function getVersion() {
		return "Ver:1 Ed:" . $this->edition;
	}

	// a description to be shown on the installed plugins listing
	function getDescription() {
		return 'Adds rel-me links to profile pages. Enabling Google Authorship verification. 
                    This plugin updates without being reinstalled. Simply update subscription list 
                    when a newer edition is uploaded. Allows p/h/em/strong tags to be shown.';
	}

        function markup($text,$memberinfo){
            $text = str_replace('<%name%>', $memberinfo->displayname, $text);
            $text = strip_tags($text, '<p><h1><h2><h3><h4><h5><em><strong>');
            return $text;
        }
        
	function doSkinVar($skinType) {
		if($skinType=='member'){
                    global $memberinfo;
                    
                    $final = array();
                    
                    if(trim($memberinfo->url)!=''){
                        $final[$memberinfo->url]=$this->markup($this->getMemberOption($memberinfo->id, 'WebLinkText'),$memberinfo);
                    } 
                    
                    //G+
                    $GPlus = $this->getMemberOption($memberinfo->id, 'GPlus');
                    if(trim($GPlus)!=''){
                        $final['https://plus.google.com/u/0/'.$GPlus]=$this->markup($this->getMemberOption($memberinfo->id, 'GPlusText'),$memberinfo);
                    }
                    
                    //Facebook
                    $Facebook = $this->getMemberOption($memberinfo->id, 'Facebook');
                    if(trim($GPlus)!=''){
                        $final['https://facebook.com/'.$Facebook]=$this->markup($this->getMemberOption($memberinfo->id, 'FacebookText'),$memberinfo);
                    }
                    
                    //Twitter
                    $Twitter = $this->getMemberOption($memberinfo->id, 'Twitter');
                    if(trim($GPlus)!=''){
                        $final['https://twitter.com/'.$Twitter]=$this->markup($this->getMemberOption($memberinfo->id, 'TwitterText'),$memberinfo);
                    }
                    
                    
                    if(count($final)>0){
                        echo $this->markup( $this->getMemberOption( $memberinfo->id,'IntroText' ), $memberinfo);
                        echo "\n\n<ul class='social contact'>\n";
                        foreach($final as $link=>$text){
                            echo "<li class='me'><a href='{$link}' rel='me'>{$text}</a></li>\n";
                        }
                        echo "</ul>\n\n";
                    }
                }
	}

	function supportsFeature ($what) {
		switch ($what) {
			case 'SqlTablePrefix':
				return 1;
			case 'SqlApi':
				return 1;
			default:
				return 0;
		}
	}
        function getEventList(){
            $this->update();
            return array();
        }
        
        function update(){
            $what = (string) $this->getOption('version');
            switch($what){
                case '0':
                case '1':
                    $this->createMemberOption('IntroText', 'Introduce Your contact options', 'textarea', '<h3>More ways to contact <%name%></h3>\n<p>You can connect with <%name%> in the following ways:</p>');
                    
                case '2': 
                    $this->createMemberOption('WebLinkText', 'Text for the user defined link', 'text', 'Visit <%name%>\'s website');
                    
                case '3':
                    
            }
            // set up to date
            $this->setOption('version', "{$this->edition}");
        }
	function install() { 
                $this->createMemberOption('GPlus', 'Your G+ ID number. Leave blank to not use', 'text', '');
                $this->createMemberOption('GPlusText', 'Text for the G+ link', 'text', '<%name%>\'s Google+ Profile');
                $this->createMemberOption('Twitter', 'Your twitter username. Leave blank to not use', 'text', '');
                $this->createMemberOption('TwitterText', 'Text for the twitter link', 'text', 'Follow <%name%> on Twitter');
                $this->createMemberOption('Facebook', 'Your Facebook custome URL part. Leave blank to not use', 'text', '');
                $this->createMemberOption('FacebookText', 'Text for the Facebook link', 'text', 'Follow <%name%> on Facebook');
               
                // enable seemless updates without uninstalling
                $this->createOption('version', 'Version of Plugin', 'text', "0");
                $this->update();

	}
}
