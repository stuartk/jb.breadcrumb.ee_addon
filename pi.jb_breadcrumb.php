<?php

$plugin_info = array(
	'pi_name'        => 'JB Breadcrumb',
	'pi_version'     => '1.0',
	'pi_author'      => 'Joel Bradbury',
	'pi_author_url'  => 'http://jo3l.net/',
	'pi_description' => 'A breadcrumb generator that tries to be a bit nicer and use titles and better dates'
);

class JB_Breadcrumb {

	var $return_data;

		function JB_Breadcrumb(){		
			global $DB, $IN, $PREFS, $TMPL, $FNS;

			// Get parameters
			$separator = (isset($IN->global_vars['bread_sep']) ? $IN->global_vars['bread_sep'] : '&raquo;');
			
			// Are we passed a URI to work from?
			// If not use current URI
			$uri = $IN->URI;

			$crumbs = array();
			$crumbs[] = '<p class="breadcrumb">You are here: <a href="/">Home</a>';
			$uri_segs = $IN->SEGS;
			//get rid of things we dont want from the array
			if(is_numeric(end($uri_segs)) && $uri_segs[sizeof($uri_segs)-1]=='page') {
				unset($uri_segs[sizeof($uri_segs)]);
				unset($uri_segs[sizeof($uri_segs)]);
			} else if($uri_segs[1]=='search' && $uri_segs[2]=='results'){	
				unset($uri_segs[sizeof($uri_segs)]);
			} else if(is_numeric(end($uri_segs)) && $uri_segs[sizeof($uri_segs)-1]=='gallery'){
				unset($uri_segs[sizeof($uri_segs)]);
			} 

			$i = 1; 
			$length = sizeof($uri_segs);
			foreach ($uri_segs as $this_seg) {
				$prev_seg = isset($uri_segs[$i-1]) ? $uri_segs[$i-1] : '';
				$i++;
				$path = "/";
				for($j=1; $j<$i;$j++){
					$path .= $uri_segs[$j].'/';	
				}				
				$sql = "SELECT url_title,title,entry_id FROM exp_weblog_titles WHERE url_title = '".$this_seg."' LIMIT 0,1";
			  $results = $DB->query($sql);
				$this_title = ucfirst($this_seg);
				//this make the year and month bits pretty - how nice
				if(is_numeric($this_title)){
					if($prev_seg=='news' || $prev_seg=='events'){
						$this_title = '20'.$this_title;
					} else if($this_title <= 12){
						switch($this_title) {
							case '1':	$this_title = 'January'; break;
							case '2':	$this_title = 'February'; break;
							case '3':	$this_title = 'March'; break;
							case '4':	$this_title = 'April'; break;
							case '5': $this_title = 'May'; break;
							case '6': $this_title = 'June'; break;
							case '7': $this_title = 'July'; break;
							case '8': $this_title = 'August'; break;
							case '9': $this_title = 'September'; break;
							case '10': $this_title = 'October'; break;
							case '11': $this_title = 'November'; break;
							case '12': $this_title = 'December'; break;
						}
					}
				} 
				if($results->num_rows>0) $this_title = $results->row['title'];

				//we dont want the final segment a link (it'll be the current page)
				if($i == $length+1) $crumbs[] = $this_title;
				else $crumbs[] = '<a href="'.$path.'">'.$this_title.'</a>';

			}

			//return $html;
			for($i=0, $count=count($crumbs); $i < $count; $i++) {
				if(!empty( $separator ) && $i != ($count-1))	{
					$crumbs[$i] = "{$crumbs[$i]} {$separator} ";
				}
			}
			$crumbs[] = '</p>';
	   $this->return_data = implode('', $crumbs);
		}
}
