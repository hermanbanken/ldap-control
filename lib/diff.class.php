<?php
class Diff {
	private $diff;
	
	public function __construct($diff, $dir_orig = false, $dir_new = false){
		$this->diff = $diff;
		$this->dir_orig = $dir_orig;
		$this->dir_new = $dir_new;
	}
	
	public function __toString(){
		$lines = explode("\n", $this->diff);
		$out = '';
		$changes = array();
		while($line = current($lines)){
			next($lines);
			
			// File names
			if(preg_match('/^diff (?<options>.*) (?<from>.*) (?<to>.*)$/', $line, $m)){
				if($this->dir_orig) 
					$m['from'] = str_replace($this->dir_orig, '.', $m['from']);
				if($this->dir_new) 
					$m['to'] = str_replace($this->dir_new, '.', $m['from']);
				$changes[] = array("old"=>$m['from'], "new"=>$m['to'], "mods" => array());
				$out .= "<tr class='file'><th class='orig'>Old</th><th class='new'>New</th><th>".$m['from']." -> ".$m['to']."</th></tr>\n";		
			}
			// Modification identifiers
			elseif (preg_match('/^((?<o_s>[0-9]+)(,(?<o_e>[0-9])+)?)(?<mod>[a-z])((?<n_s>[0-9]+)(,(?<n_e>[0-9])+)?)$/', $line, $m)){
				$mod = $m;
				$changes[count($changes)-1]['mods'][] = $m;
				$mod['o'] = $mod['o_s'];
				$mod['n'] = $mod['n_s'];
			}
			// Changed line
			elseif (preg_match('/^(?<what>[\<\>\\-]|-{3})( (?<code>.*))?$/', $line, $m)) {
				$class = 'cod';
				$out .= "<tr>";
				if($m['what'] == '<'){
					$class.= ' deleted';
					$out .= "<td class='orig'>".($mod['o']++)."</td><td class='new'>&nbsp;</td>";
				} elseif($m['what'] == '>'){
					$class.= ' added';
					$out .= "<td class='orig'>&nbsp;</td><td class='new'>".($mod['n']++)."</td>";
				}
				if( $m['what'] != '---')
					$out.= "<td width='100%' class='$class'><pre>".$m['code']."</pre></td>\n";
				
				$out .= "</tr>";
			}
		}
		$this->changes = $changes;
		return "<table class='code-diff'>$out</table>";
	}
	
	public static function test(){}
}

// Direct access: test
if (!defined('BASEPATH')) Diff::test();
?>