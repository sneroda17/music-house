<?php 
	/**
	* Custom function used by the script
	*/
	class Custom
	{

		public static function popularity($points) {
		    $tmp = 0;
		    $init = 0;
		    $factor = 100;
		    $max = 0;
		    do{
		        $tmp = $factor*$init;
		        $init++;
		        $max = $factor*$init;
		    }
		    while(($points - $tmp) > $factor);

		    return $points/$max*100;

		}

		public static function imgUpload($imgFile, $name, $location, $thumbnail = false, $is_url = false) {
	        $dir = md5(date('FY'));
	        if($location == 'assets')
	        {
	        	$mainDir = 'assets/images/';
	        }
	        else
	        {
	        	$mainDir = 'uploads/' . $location . '/' . $dir . '/';
	        }

	        if (!file_exists($mainDir)) {
                mkdir($mainDir, 0777, true);
            }

            if ($is_url) {
	        	$file = file_get_contents($imgFile);
                file_put_contents($mainDir . $name . '.jpg', $file);
            } else {
	            if($location == 'assets') {
					$uploadSuccess = $imgFile->move($mainDir, $name . '.png');
					return;
				} else {
	            	$uploadSuccess = $imgFile->move($mainDir, $name . '.jpg');
	            }
	        }


            $name .= '.jpg';
            $imgRes = Image::make($mainDir.$name);
            if ($location == 'albums') {
                $imgRes->resize(
                    960,
                    null,
                    function ($constraint) {
                        $constraint->aspectRatio();
                    }
                );
            } elseif ($location == 'artists') {
                $imgRes->resize(175, 175);
            }

			$imgRes->save($mainDir.$name);

            if($thumbnail) {
	        	$thumbDir = $mainDir . 'thumb/';
	        	if (!file_exists($thumbDir)) {
	                mkdir($thumbDir, 0777, true);
	            }
	            copy($mainDir.$name, $thumbDir.$name);
                $img_t =  Image::make($thumbDir.$name)->resize(175, 175);
                $img_t->save($thumbDir.$name);
	        }

	        return $dir;
	    }
        public static function imgUploadBase64($imgFile,$name) {
            $dir = md5(date('FY'));
            $location =  "albums";
            $thumbnail = true;
            $mainDir = 'uploads/' . $location . '/' . $dir . '/';
            if (!file_exists($mainDir)) {
                mkdir($mainDir, 0777, true);
            }

            $name .= '.jpg';
            $imgRes = Image::make(file_get_contents($imgFile));
            //if ($location == 'albums') {
                $imgRes->resize(
                    960,
                    null,
                    function ($constraint) {
                        $constraint->aspectRatio();
                    }
                );
            //} elseif ($location == 'artists') {
            //    $imgRes->resize(175, 175);
            //}

            $imgRes->save($mainDir.$name);

            if($thumbnail) {
                $thumbDir = $mainDir . 'thumb/';
                if (!file_exists($thumbDir)) {
                    mkdir($thumbDir, 0777, true);
                }
                copy($mainDir.$name, $thumbDir.$name);
                $img_t =  Image::make($thumbDir.$name)->resize(175, 175);
                $img_t->save($thumbDir.$name);
            }

            return $dir;
        }
        public static function bulkUploadAudio($audio, $filename)
        {
            $dir = md5(date('FY'));
            $mainDir = 'uploads/audios/' . $dir . '/';
            $wavefileDir = 'uploads/audios/' . $dir . '/wavefiles/';
            $orgFileDir = 'uploads/audios/' . $dir . '/org/';

            if (!file_exists($mainDir)) {
                mkdir($mainDir, 0777, true);
            }
            if (!file_exists($wavefileDir)) {
                mkdir($wavefileDir, 0777, true);
            }
            if (!file_exists($orgFileDir)) {
                mkdir($orgFileDir, 0777, true);
            }
            $mp3copyTo = $mainDir.$filename . '.mp3';

            $command =      '`ffmpeg -i "' . $audio . '"  -b:a 128k   -map_metadata 0 -threads 1 -id3v2_version 3 -write_id3v1 1 -c:v copy  "' . $mp3copyTo . '"`';

            @exec($command);

            $pngCopyTo  = $wavefileDir.$filename . '.png';

            $command_png = '`ffmpeg -i "' . $audio . '" -filter_complex showwavespic=s=970x50:colors=#E3E3E3 -frames:v 1 "' . $pngCopyTo .'"`';
            @exec($command_png);

            copy($audio, $orgFileDir.$filename . '.mp3');
            return $dir;

        }
        function runCmd($cmd){

            $descriptorspec = array(
                0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
                1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
                2 => array("file", "error-output.txt", "a") // stderr is a file to write to
            );

            $cwd = '/tmp';
            $env = array();

            $process = proc_open($cmd, $descriptorspec, $pipes, $cwd, $env);

            if (is_resource($process)) {
                // $pipes now looks like this:
                // 0 => writeable handle connected to child stdin
                // 1 => readable handle connected to child stdout
                // Any error output will be appended to /tmp/error-output.txt

                fwrite($pipes[0], '<?php print_r($_ENV); ?>');
                fclose($pipes[0]);

                echo stream_get_contents($pipes[1]);
                fclose($pipes[1]);

                // It is important that you close any pipes before calling
                // proc_close in order to avoid a deadlock
                $return_value = proc_close($process);

                echo "command returned $return_value\n";
            }


        }

	    public static function uploadAudio($audio, $filename)
	    {
	        $dir = md5(date('FY'));
	        $mainDir = 'uploads/audios/' . $dir . '/';

	        if (!file_exists($mainDir)) {
	            mkdir($mainDir, 0777, true);
	        }

	        $audio->move($mainDir, $filename . '.mp3');
	        return $dir;

	    }

	    // ********** Format TimeStamps ******* //

	    public static function formatTimeStamp($timestamp)
	    {
	    	if($timestamp->diffInDays() > 30)
	    	{
            	return $timestamp->toFormattedDateString();
	        }
	        else
	        {
	            return $timestamp->diffForHumans();
	        }
	    }

	    // ********** format file size ********** //

	    public static function formatBytes($size)
	    {
	        $mod = 1024;
	        $units = explode(' ', 'B KB MB GB TB PB');
	        for ($i = 0; $size > $mod; $i++) {
	            $size /= $mod;
	        }
	        return round($size, 2) . ' ' . $units[$i];
	    }

	    // ********** Format time ********** //
	    public static function formatMilliseconds($milliseconds)
	    {
	        $seconds = floor($milliseconds / 1000);
	        $minutes = floor($seconds / 60);
	        $hours = floor($minutes / 60);
	        $milliseconds = $milliseconds % 1000;
	        $seconds = $seconds % 60;
	        $minutes = $minutes % 60;

	        $format = '%u:%02u:%02u';
	        $time = sprintf($format, $hours, $minutes, $seconds);
	        return rtrim($time, '0');
	    }

	    // ********** Slugify String ********** //

	    public static function slugify($str, $length = 7)
	    {
	        $rules = array(
	            // Numeric characters
	            '¹' => 1,
	            '²' => 2,
	            '³' => 3,
	            // Latin
	            '°' => 0,
	            'æ' => 'ae',
	            'ǽ' => 'ae',
	            'À' => 'A',
	            'Á' => 'A',
	            'Â' => 'A',
	            'Ã' => 'A',
	            'Å' => 'A',
	            'Ǻ' => 'A',
	            'Ă' => 'A',
	            'Ǎ' => 'A',
	            'Æ' => 'AE',
	            'Ǽ' => 'AE',
	            'à' => 'a',
	            'á' => 'a',
	            'â' => 'a',
	            'ã' => 'a',
	            'å' => 'a',
	            'ǻ' => 'a',
	            'ă' => 'a',
	            'ǎ' => 'a',
	            'ª' => 'a',
	            '@' => 'at',
	            'Ĉ' => 'C',
	            'Ċ' => 'C',
	            'ĉ' => 'c',
	            'ċ' => 'c',
	            '©' => 'c',
	            'Ð' => 'Dj',
	            'Đ' => 'D',
	            'ð' => 'dj',
	            'đ' => 'd',
	            'È' => 'E',
	            'É' => 'E',
	            'Ê' => 'E',
	            'Ë' => 'E',
	            'Ĕ' => 'E',
	            'Ė' => 'E',
	            'è' => 'e',
	            'é' => 'e',
	            'ê' => 'e',
	            'ë' => 'e',
	            'ĕ' => 'e',
	            'ė' => 'e',
	            'ƒ' => 'f',
	            'Ĝ' => 'G',
	            'Ġ' => 'G',
	            'ĝ' => 'g',
	            'ġ' => 'g',
	            'Ĥ' => 'H',
	            'Ħ' => 'H',
	            'ĥ' => 'h',
	            'ħ' => 'h',
	            'Ì' => 'I',
	            'Í' => 'I',
	            'Î' => 'I',
	            'Ï' => 'I',
	            'Ĩ' => 'I',
	            'Ĭ' => 'I',
	            'Ǐ' => 'I',
	            'Į' => 'I',
	            'Ĳ' => 'IJ',
	            'ì' => 'i',
	            'í' => 'i',
	            'î' => 'i',
	            'ï' => 'i',
	            'ĩ' => 'i',
	            'ĭ' => 'i',
	            'ǐ' => 'i',
	            'į' => 'i',
	            'ĳ' => 'ij',
	            'Ĵ' => 'J',
	            'ĵ' => 'j',
	            'Ĺ' => 'L',
	            'Ľ' => 'L',
	            'Ŀ' => 'L',
	            'ĺ' => 'l',
	            'ľ' => 'l',
	            'ŀ' => 'l',
	            'Ñ' => 'N',
	            'ñ' => 'n',
	            'ŉ' => 'n',
	            'Ò' => 'O',
	            'Ô' => 'O',
	            'Õ' => 'O',
	            'Ō' => 'O',
	            'Ŏ' => 'O',
	            'Ǒ' => 'O',
	            'Ő' => 'O',
	            'Ơ' => 'O',
	            'Ø' => 'O',
	            'Ǿ' => 'O',
	            'Œ' => 'OE',
	            'ò' => 'o',
	            'ô' => 'o',
	            'õ' => 'o',
	            'ō' => 'o',
	            'ŏ' => 'o',
	            'ǒ' => 'o',
	            'ő' => 'o',
	            'ơ' => 'o',
	            'ø' => 'o',
	            'ǿ' => 'o',
	            'º' => 'o',
	            'œ' => 'oe',
	            'Ŕ' => 'R',
	            'Ŗ' => 'R',
	            'ŕ' => 'r',
	            'ŗ' => 'r',
	            'Ŝ' => 'S',
	            'Ș' => 'S',
	            'ŝ' => 's',
	            'ș' => 's',
	            'ſ' => 's',
	            'Ţ' => 'T',
	            'Ț' => 'T',
	            'Ŧ' => 'T',
	            'Þ' => 'TH',
	            'ţ' => 't',
	            'ț' => 't',
	            'ŧ' => 't',
	            'þ' => 'th',
	            'Ù' => 'U',
	            'Ú' => 'U',
	            'Û' => 'U',
	            'Ũ' => 'U',
	            'Ŭ' => 'U',
	            'Ű' => 'U',
	            'Ų' => 'U',
	            'Ư' => 'U',
	            'Ǔ' => 'U',
	            'Ǖ' => 'U',
	            'Ǘ' => 'U',
	            'Ǚ' => 'U',
	            'Ǜ' => 'U',
	            'ù' => 'u',
	            'ú' => 'u',
	            'û' => 'u',
	            'ũ' => 'u',
	            'ŭ' => 'u',
	            'ű' => 'u',
	            'ų' => 'u',
	            'ư' => 'u',
	            'ǔ' => 'u',
	            'ǖ' => 'u',
	            'ǘ' => 'u',
	            'ǚ' => 'u',
	            'ǜ' => 'u',
	            'Ŵ' => 'W',
	            'ŵ' => 'w',
	            'Ý' => 'Y',
	            'Ÿ' => 'Y',
	            'Ŷ' => 'Y',
	            'ý' => 'y',
	            'ÿ' => 'y',
	            'ŷ' => 'y',
	            // Russian
	            'Ъ' => '',
	            'Ь' => '',
	            'А' => 'A',
	            'Б' => 'B',
	            'Ц' => 'C',
	            'Ч' => 'Ch',
	            'Д' => 'D',
	            'Е' => 'E',
	            'Ё' => 'E',
	            'Э' => 'E',
	            'Ф' => 'F',
	            'Г' => 'G',
	            'Х' => 'H',
	            'И' => 'I',
	            'Й' => 'J',
	            'Я' => 'Ja',
	            'Ю' => 'Ju',
	            'К' => 'K',
	            'Л' => 'L',
	            'М' => 'M',
	            'Н' => 'N',
	            'О' => 'O',
	            'П' => 'P',
	            'Р' => 'R',
	            'С' => 'S',
	            'Ш' => 'Sh',
	            'Щ' => 'Shch',
	            'Т' => 'T',
	            'У' => 'U',
	            'В' => 'V',
	            'Ы' => 'Y',
	            'З' => 'Z',
	            'Ж' => 'Zh',
	            'ъ' => '',
	            'ь' => '',
	            'а' => 'a',
	            'б' => 'b',
	            'ц' => 'c',
	            'ч' => 'ch',
	            'д' => 'd',
	            'е' => 'e',
	            'ё' => 'e',
	            'э' => 'e',
	            'ф' => 'f',
	            'г' => 'g',
	            'х' => 'h',
	            'и' => 'i',
	            'й' => 'j',
	            'я' => 'ja',
	            'ю' => 'ju',
	            'к' => 'k',
	            'л' => 'l',
	            'м' => 'm',
	            'н' => 'n',
	            'о' => 'o',
	            'п' => 'p',
	            'р' => 'r',
	            'с' => 's',
	            'ш' => 'sh',
	            'щ' => 'shch',
	            'т' => 't',
	            'у' => 'u',
	            'в' => 'v',
	            'ы' => 'y',
	            'з' => 'z',
	            'ж' => 'zh',
	            // German characters
	            'Ä' => 'AE',
	            'Ö' => 'OE',
	            'Ü' => 'UE',
	            'ß' => 'ss',
	            'ä' => 'ae',
	            'ö' => 'oe',
	            'ü' => 'ue',
	            // Turkish characters
	            'Ç' => 'C',
	            'Ğ' => 'G',
	            'İ' => 'I',
	            'Ş' => 'S',
	            'ç' => 'c',
	            'ğ' => 'g',
	            'ı' => 'i',
	            'ş' => 's',
	            // Latvian
	            'Ā' => 'A',
	            'Ē' => 'E',
	            'Ģ' => 'G',
	            'Ī' => 'I',
	            'Ķ' => 'K',
	            'Ļ' => 'L',
	            'Ņ' => 'N',
	            'Ū' => 'U',
	            'ā' => 'a',
	            'ē' => 'e',
	            'ģ' => 'g',
	            'ī' => 'i',
	            'ķ' => 'k',
	            'ļ' => 'l',
	            'ņ' => 'n',
	            'ū' => 'u',
	            // Ukrainian
	            'Ґ' => 'G',
	            'І' => 'I',
	            'Ї' => 'Ji',
	            'Є' => 'Ye',
	            'ґ' => 'g',
	            'і' => 'i',
	            'ї' => 'ji',
	            'є' => 'ye',
	            // Czech
	            'Č' => 'C',
	            'Ď' => 'D',
	            'Ě' => 'E',
	            'Ň' => 'N',
	            'Ř' => 'R',
	            'Š' => 'S',
	            'Ť' => 'T',
	            'Ů' => 'U',
	            'Ž' => 'Z',
	            'č' => 'c',
	            'ď' => 'd',
	            'ě' => 'e',
	            'ň' => 'n',
	            'ř' => 'r',
	            'š' => 's',
	            'ť' => 't',
	            'ů' => 'u',
	            'ž' => 'z',
	            // Polish
	            'Ą' => 'A',
	            'Ć' => 'C',
	            'Ę' => 'E',
	            'Ł' => 'L',
	            'Ń' => 'N',
	            'Ó' => 'O',
	            'Ś' => 'S',
	            'Ź' => 'Z',
	            'Ż' => 'Z',
	            'ą' => 'a',
	            'ć' => 'c',
	            'ę' => 'e',
	            'ł' => 'l',
	            'ń' => 'n',
	            'ó' => 'o',
	            'ś' => 's',
	            'ź' => 'z',
	            'ż' => 'z',
	            // Greek
	            'Α' => 'A',
	            'Β' => 'B',
	            'Γ' => 'G',
	            'Δ' => 'D',
	            'Ε' => 'E',
	            'Ζ' => 'Z',
	            'Η' => 'E',
	            'Θ' => 'Th',
	            'Ι' => 'I',
	            'Κ' => 'K',
	            'Λ' => 'L',
	            'Μ' => 'M',
	            'Ν' => 'N',
	            'Ξ' => 'X',
	            'Ο' => 'O',
	            'Π' => 'P',
	            'Ρ' => 'R',
	            'Σ' => 'S',
	            'Τ' => 'T',
	            'Υ' => 'Y',
	            'Φ' => 'Ph',
	            'Χ' => 'Ch',
	            'Ψ' => 'Ps',
	            'Ω' => 'O',
	            'Ϊ' => 'I',
	            'Ϋ' => 'Y',
	            'ά' => 'a',
	            'έ' => 'e',
	            'ή' => 'e',
	            'ί' => 'i',
	            'ΰ' => 'Y',
	            'α' => 'a',
	            'β' => 'b',
	            'γ' => 'g',
	            'δ' => 'd',
	            'ε' => 'e',
	            'ζ' => 'z',
	            'η' => 'e',
	            'θ' => 'th',
	            'ι' => 'i',
	            'κ' => 'k',
	            'λ' => 'l',
	            'μ' => 'm',
	            'ν' => 'n',
	            'ξ' => 'x',
	            'ο' => 'o',
	            'π' => 'p',
	            'ρ' => 'r',
	            'ς' => 's',
	            'σ' => 's',
	            'τ' => 't',
	            'υ' => 'y',
	            'φ' => 'ph',
	            'χ' => 'ch',
	            'ψ' => 'ps',
	            'ω' => 'o',
	            'ϊ' => 'i',
	            'ϋ' => 'y',
	            'ό' => 'o',
	            'ύ' => 'y',
	            'ώ' => 'o',
	            'ϐ' => 'b',
	            'ϑ' => 'th',
	            'ϒ' => 'Y',
	            /* Arabic */
	            'أ' => 'a',
	            'ب' => 'b',
	            'ت' => 't',
	            'ث' => 'th',
	            'ج' => 'g',
	            'ح' => 'h',
	            'خ' => 'kh',
	            'د' => 'd',
	            'ذ' => 'th',
	            'ر' => 'r',
	            'ز' => 'z',
	            'س' => 's',
	            'ش' => 'sh',
	            'ص' => 's',
	            'ض' => 'd',
	            'ط' => 't',
	            'ظ' => 'th',
	            'ع' => 'aa',
	            'غ' => 'gh',
	            'ف' => 'f',
	            'ق' => 'k',
	            'ك' => 'k',
	            'ل' => 'l',
	            'م' => 'm',
	            'ن' => 'n',
	            'ه' => 'h',
	            'و' => 'o',
	            'ي' => 'y',
	            /* Vietnamese */
	            'ạ' => 'a',
	            'ả' => 'a',
	            'ầ' => 'a',
	            'ấ' => 'a',
	            'ậ' => 'a',
	            'ẩ' => 'a',
	            'ẫ' => 'a',
	            'ằ' => 'a',
	            'ắ' => 'a',
	            'ặ' => 'a',
	            'ẳ' => 'a',
	            'ẵ' => 'a',
	            'ẹ' => 'e',
	            'ẻ' => 'e',
	            'ẽ' => 'e',
	            'ề' => 'e',
	            'ế' => 'e',
	            'ệ' => 'e',
	            'ể' => 'e',
	            'ễ' => 'e',
	            'ị' => 'i',
	            'ỉ' => 'i',
	            'ọ' => 'o',
	            'ỏ' => 'o',
	            'ồ' => 'o',
	            'ố' => 'o',
	            'ộ' => 'o',
	            'ổ' => 'o',
	            'ỗ' => 'o',
	            'ờ' => 'o',
	            'ớ' => 'o',
	            'ợ' => 'o',
	            'ở' => 'o',
	            'ỡ' => 'o',
	            'ụ' => 'u',
	            'ủ' => 'u',
	            'ừ' => 'u',
	            'ứ' => 'u',
	            'ự' => 'u',
	            'ử' => 'u',
	            'ữ' => 'u',
	            'ỳ' => 'y',
	            'ỵ' => 'y',
	            'ỷ' => 'y',
	            'ỹ' => 'y',
	            'Ạ' => 'A',
	            'Ả' => 'A',
	            'Ầ' => 'A',
	            'Ấ' => 'A',
	            'Ậ' => 'A',
	            'Ẩ' => 'A',
	            'Ẫ' => 'A',
	            'Ằ' => 'A',
	            'Ắ' => 'A',
	            'Ặ' => 'A',
	            'Ẳ' => 'A',
	            'Ẵ' => 'A',
	            'Ẹ' => 'E',
	            'Ẻ' => 'E',
	            'Ẽ' => 'E',
	            'Ề' => 'E',
	            'Ế' => 'E',
	            'Ệ' => 'E',
	            'Ể' => 'E',
	            'Ễ' => 'E',
	            'Ị' => 'I',
	            'Ỉ' => 'I',
	            'Ọ' => 'O',
	            'Ỏ' => 'O',
	            'Ồ' => 'O',
	            'Ố' => 'O',
	            'Ộ' => 'O',
	            'Ổ' => 'O',
	            'Ỗ' => 'O',
	            'Ờ' => 'O',
	            'Ớ' => 'O',
	            'Ợ' => 'O',
	            'Ở' => 'O',
	            'Ỡ' => 'O',
	            'Ụ' => 'U',
	            'Ủ' => 'U',
	            'Ừ' => 'U',
	            'Ứ' => 'U',
	            'Ự' => 'U',
	            'Ử' => 'U',
	            'Ữ' => 'U',
	            'Ỳ' => 'Y',
	            'Ỵ' => 'Y',
	            'Ỷ' => 'Y',
	            'Ỹ' => 'Y',
	            // burmese consonants
	            'က' => 'k',
	            'ခ' => 'kh',
	            'ဂ' => 'g',
	            'ဃ' => 'ga',
	            'င' => 'ng',
	            'စ' => 's',
	            'ဆ' => 'sa',
	            'ဇ' => 'z',
	            'စျ' => 'za',
	            'ည' => 'ny',
	            'ဋ' => 't',
	            'ဌ' => 'ta',
	            'ဍ' => 'd',
	            'ဎ' => 'da',
	            'ဏ' => 'na',
	            'တ' => 't',
	            'ထ' => 'ta',
	            'ဒ' => 'd',
	            'ဓ' => 'da',
	            'န' => 'n',
	            'ပ' => 'p',
	            'ဖ' => 'pa',
	            'ဗ' => 'b',
	            'ဘ' => 'ba',
	            'မ' => 'm',
	            'ယ' => 'y',
	            'ရ' => 'ya',
	            'လ' => 'l',
	            'ဝ' => 'w',
	            'သ' => 'th',
	            'ဟ' => 'h',
	            'ဠ' => 'la',
	            'အ' => 'a',
	            // consonant character combos
	            'ြ' => 'y',
	            'ျ' => 'ya',
	            'ွ' => 'w',
	            'ြွ' => 'yw',
	            'ျွ' => 'ywa',
	            'ှ' => 'h',
	            // independent vowels
	            'ဧ' => 'e',
	            '၏' => '-e',
	            'ဣ' => 'i',
	            'ဤ' => '-i',
	            'ဉ' => 'u',
	            'ဦ' => '-u',
	            'ဩ' => 'aw',
	            'သြော' => 'aw',
	            'ဪ' => 'aw',
	            '၍' => 'ywae',
	            '၌' => 'hnaik',
	            // numbers
	            '၀' => '0',
	            '၁' => '1',
	            '၂' => '2',
	            '၃' => '3',
	            '၄' => '4',
	            '၅' => '5',
	            '၆' => '6',
	            '၇' => '7',
	            '၈' => '8',
	            '၉' => '9',
	            // virama and tone marks which are silent in transliteration
	            '္' => '',
	            '့' => '',
	            'း' => '',
	            // dependent vowels
	            'ာ' => 'a',
	            'ါ' => 'a',
	            'ေ' => 'e',
	            'ဲ' => 'e',
	            'ိ' => 'i',
	            'ီ' => 'i',
	            'ို' => 'o',
	            'ု' => 'u',
	            'ူ' => 'u',
	            'ေါင်' => 'aung',
	            'ော' => 'aw',
	            'ော်' => 'aw',
	            'ေါ' => 'aw',
	            'ေါ်' => 'aw',
	            '်' => 'at',
	            'က်' => 'et',
	            'ိုက်' => 'aik',
	            'ောက်' => 'auk',
	            'င်' => 'in',
	            'ိုင်' => 'aing',
	            'ောင်' => 'aung',
	            'စ်' => 'it',
	            'ည်' => 'i',
	            'တ်' => 'at',
	            'ိတ်' => 'eik',
	            'ုတ်' => 'ok',
	            'ွတ်' => 'ut',
	            'ေတ်' => 'it',
	            'ဒ်' => 'd',
	            'ိုဒ်' => 'ok',
	            'ုဒ်' => 'ait',
	            'န်' => 'an',
	            'ာန်' => 'an',
	            'ိန်' => 'ein',
	            'ုန်' => 'on',
	            'ွန်' => 'un',
	            'ပ်' => 'at',
	            'ိပ်' => 'eik',
	            'ုပ်' => 'ok',
	            'ွပ်' => 'ut',
	            'န်ုပ်' => 'nub',
	            'မ်' => 'an',
	            'ိမ်' => 'ein',
	            'ုမ်' => 'on',
	            'ွမ်' => 'un',
	            'ယ်' => 'e',
	            'ိုလ်' => 'ol',
	            'ဉ်' => 'in',
	            'ံ' => 'an',
	            'ိံ' => 'ein',
	            'ုံ' => 'on'
	            );
	        $str = strtolower(strtr($str, $rules));
	        $str = preg_replace('/([^a-z0-9]|-)+/', '-', $str);
	        $str = strtolower($str);
	        $str = trim($str, '-');

	        if (empty($str)) {
	            return str_random($length);
	        }

	        return $str;
	    }
	}

?>