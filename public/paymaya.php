<?php
class userAgent {
    /**
     * Windows Operating System list with dynamic versioning
     * @var array $windows_os
     */
    public $windows_os = [ '[Windows; |Windows; U; |]Windows NT 6.:number0-3:;[ Win64; x64| WOW64| x64|]',
                           '[Windows; |Windows; U; |]Windows NT 10.:number0-5:;[ Win64; x64| WOW64| x64|]', ];
    /**
     * Linux Operating Systems [limited]
     * @var array $linux_os
     */
    public $linux_os = [ '[Linux; |][U; |]Linux x86_64',
                         '[Linux; |][U; |]Linux i:number5-6::number4-8::number0-6: [x86_64|]' ];
    /**
     * Mac Operating System (OS X) with dynamic versioning
     * @var array $mac_os
     */
    public $mac_os = [ 'Macintosh; [U; |]Intel Mac OS X :number7-9:_:number0-9:_:number0-9:',
                       'Macintosh; [U; |]Intel Mac OS X 10_:number0-12:_:number0-9:' ];
    /**
     * Versions of Android to be used
     * @var array $androidVersions
     */
    public $androidVersions = [ '4.3.1',
                                '4.4',
                                '4.4.1',
                                '4.4.4',
                                '5.0',
                                '5.0.1',
                                '5.0.2',
                                '5.1',
                                '5.1.1',
                                '6.0',
                                '6.0.1',
                                '7.0',
                                '7.1',
                                '7.1.1' ];
    /**
     * Holds the version of android for the User Agent being generated
     * @property string $androidVersion
     */
    public $androidVersion;
    /**
     * Android devices and for specific android versions
     * @var array $androidDevices
     */
    public $androidDevices = [ '4.3' => [ 'GT-I9:number2-5:00 Build/JDQ39',
                                          'Nokia 3:number1-3:[10|15] Build/IMM76D',
                                          '[SAMSUNG |]SM-G3:number1-5:0[R5|I|V|A|T|S] Build/JLS36C',
                                          'Ascend G3:number0-3:0 Build/JLS36I',
                                          '[SAMSUNG |]SM-G3:number3-6::number1-8::number0-9:[V|A|T|S|I|R5] Build/JLS36C',
                                          'HUAWEI G6-L:number10-11: Build/HuaweiG6-L:number10-11:',
                                          '[SAMSUNG |]SM-[G|N]:number7-9:1:number0-8:[S|A|V|T] Build/[JLS36C|JSS15J]',
                                          '[SAMSUNG |]SGH-N0:number6-9:5[T|V|A|S] Build/JSS15J',
                                          'Samsung Galaxy S[4|IV] Mega GT-I:number89-95:00 Build/JDQ39',
                                          'SAMSUNG SM-T:number24-28:5[s|a|t|v] Build/[JLS36C|JSS15J]',
                                          'HP :number63-73:5 Notebook PC Build/[JLS36C|JSS15J]',
                                          'HP Compaq 2:number1-3:10b Build/[JLS36C|JSS15J]',
                                          'HTC One 801[s|e] Build/[JLS36C|JSS15J]',
                                          'HTC One max Build/[JLS36C|JSS15J]',
                                          'HTC Xplorer A:number28-34:0[e|s] Build/GRJ90', ],
                               '4.4' => [ 'XT10:number5-8:0 Build/SU6-7.3',
                                          'XT10:number12-52: Build/[KXB20.9|KXC21.5]',
                                          'Nokia :number30-34:10 Build/IMM76D',
                                          'E:number:20-23::number0-3::number0-4: Build/24.0.[A|B].1.34',
                                          '[SAMSUNG |]SM-E500[F|L] Build/KTU84P',
                                          'LG Optimus G Build/KRT16M',
                                          'LG-E98:number7-9: Build/KOT49I',
                                          'Elephone P:number2-6:000 Build/KTU84P',
                                          'IQ450:number0-4: Quad Build/KOT49H',
                                          'LG-F:number2-5:00[K|S|L] Build/KOT49[I|H]',
                                          'LG-V:number3-7::number0-1:0 Build/KOT49I',
                                          '[SAMSUNG |]SM-J:number1-2::number0-1:0[G|F] Build/KTU84P',
                                          '[SAMSUNG |]SM-N80:number0-1:0 Build/[KVT49L|JZO54K]',
                                          '[SAMSUNG |]SM-N900:number5-8: Build/KOT49H',
                                          '[SAMSUNG-|]SGH-I337[|M] Build/[JSS15J|KOT49H]',
                                          '[SAMSUNG |]SM-G900[W8|9D|FD|H|V|FG|A|T] Build/KOT49H',
                                          '[SAMSUNG |]SM-T5:number30-35: Build/[KOT49H|KTU84P]',
                                          '[Google |]Nexus :number5-7: Build/KOT49H',
                                          'LG-H2:number0-2:0 Build/KOT49[I|H]',
                                          'HTC One[_M8|_M9|0P6B|801e|809d|0P8B2|mini 2|S][ dual sim|] Build/[KOT49H|KTU84L]',
                                          '[SAMSUNG |]GT-I9:number3-5:0:number0-6:[V|I|T|N] Build/KOT49H',
                                          'Lenovo P7:number7-8::number1-6: Build/[Lenovo|JRO03C]',
                                          'LG-D95:number1-8: Build/KOT49[I|H]',
                                          'LG-D:number1-8::number0-8:0 Build/KOT49[I|H]',
                                          'Nexus5 V:number6-7:.1 Build/KOT49H',
                                          'Nexus[_|] :number4-10: Build/[KOT49H|KTU84P]',
                                          'Nexus[_S_| S ][4G |]Build/GRJ22',
                                          '[HM NOTE|NOTE-III|NOTE2 1LTE[TD|W|T]',
                                          'ALCATEL ONE[| ]TOUCH 70:number2-4::number0-9:[X|D|E|A] Build/KOT49H',
                                          'MOTOROLA [MOTOG|MSM8960|RAZR] Build/KVT49L' ],
                               '5.0' => [ 'Nokia :number10-11:00 [wifi|4G|LTE] Build/GRK39F',
                                          'HTC 80:number1-2[s|w|e|t] Build/[LRX22G|JSS15J]',
                                          'Lenovo A7000-a Build/LRX21M;',
                                          'HTC Butterfly S [901|919][s|d|] Build/LRX22G',
                                          'HTC [M8|M9|M8 Pro Build/LRX22G',
                                          'LG-D3:number25-37: Build/LRX22G',
                                          'LG-D72:number0-9: Build/LRX22G',
                                          '[SAMSUNG |]SM-G4:number0-9:0 Build/LRX22[G|C]',
                                          '[|SAMSUNG ]SM-G9[00|25|20][FD|8|F|F-ORANGE|FG|FQ|H|I|L|M|S|T] Build/[LRX21T|KTU84F|KOT49H]',
                                          '[SAMSUNG |]SM-A:number7-8:00[F|I|T|H|] Build/[LRX22G|LMY47X]',
                                          '[SAMSUNG-|]SM-N91[0|5][A|V|F|G|FY] Build/LRX22C',
                                          '[SAMSUNG |]SM-[T|P][350|550|555|355|805|800|710|810|815] Build/LRX22G',
                                          'LG-D7:number0-2::number0-9: Build/LRX22G',
                                          '[LG|SM]-[D|G]:number8-9::number0-5::number0-9:[|P|K|T|I|F|T1] Build/[LRX22G|KOT49I|KVT49L|LMY47X]' ],
                               '5.1' => [ 'Nexus :number5-9: Build/[LMY48B|LRX22C]',
                                          '[|SAMSUNG ]SM-G9[28|25|20][X|FD|8|F|F-ORANGE|FG|FQ|H|I|L|M|S|T] Build/[LRX22G|LMY47X]',
                                          '[|SAMSUNG ]SM-G9[35|350][X|FD|8|F|F-ORANGE|FG|FQ|H|I|L|M|S|T] Build/[MMB29M|LMY47X]',
                                          '[MOTOROLA |][MOTO G|MOTO G XT1068|XT1021|MOTO E XT1021|MOTO XT1580|MOTO X FORCE XT1580|MOTO X PLAY XT1562|MOTO XT1562|MOTO XT1575|MOTO X PURE XT1575|MOTO XT1570 MOTO X STYLE] Build/[LXB22|LMY47Z|LPC23|LPK23|LPD23|LPH223]' ],
                               '6.0' => [ '[SAMSUNG |]SM-[G|D][920|925|928|9350][V|F|I|L|M|S|8|I] Build/[MMB29K|MMB29V|MDB08I|MDB08L]',
                                          'Nexus :number5-7:[P|X|] Build/[MMB29K|MMB29V|MDB08I|MDB08L]',
                                          'HTC One[_| ][M9|M8|M8 Pro] Build/MRA58K',
                                          'HTC One[_M8|_M9|0P6B|801e|809d|0P8B2|mini 2|S][ dual sim|] Build/MRA58K' ],
                               '7.0' => [ 'Pixel [XL|C] Build/[NRD90M|NME91E]',
                                          'Nexus :number5-9:[X|P|] Build/[NPD90G|NME91E]',
                                          '[SAMSUNG |]GT-I:number91-98:00 Build/KTU84P',
                                          'Xperia [V |]Build/NDE63X',
                                          'LG-H:number90-93:0 Build/NRD90[C|M]' ],
                               '7.1' => [ 'Pixel [XL|C] Build/[NRD90M|NME91E]',
                                          'Nexus :number5-9:[X|P|] Build/[NPD90G|NME91E]',
                                          '[SAMSUNG |]GT-I:number91-98:00 Build/KTU84P',
                                          'Xperia [V |]Build/NDE63X',
                                          'LG-H:number90-93:0 Build/NRD90[C|M]' ] ];
    /**
     * List of "OS" strings used for android
     * @var array $android_os
     */
    public $android_os = [ 'Android :androidVersion:; :androidDevice:',
                           //TODO: Add a $windowsDevices variable that does the same as androidDevice
                           //'Windows Phone 10.0; Android :androidVersion:; :windowsDevice:',
                           'Android :androidVersion:; :androidDevice:',
                           'Android; Android :androidVersion:; :androidDevice:', ];
    /**
     * List of "OS" strings used for iOS
     * @var array $mobile_ios
     */
    public $mobile_ios = [ 'iphone' => 'iPhone; CPU iPhone OS :number7-11:_:number0-9:_:number0-9:; like Mac OS X;',
                           'ipad' => 'iPad; CPU iPad OS :number7-11:_:number0-9:_:number0-9: like Mac OS X;',
                           'ipod' => 'iPod; CPU iPod OS :number7-11:_:number0-9:_:number0-9:; like Mac OS X;', ];
    
    /**
     * Get a random operating system
     * @param null|string $os
     * @return string *
     */
    public function getOS($os = NULL) {
        $_os = [];
        if($os === NULL || in_array($os, [ 'chrome', 'firefox', 'explorer' ])) {
            $_os = $os === 'explorer' ? $this->windows_os : array_merge($this->windows_os, $this->linux_os, $this->mac_os);
        } else {
            $_os += $this->{$os . '_os'};
        }
        // randomly select on operating system
        $selected_os = rtrim($_os[random_int(0, count($_os) - 1)], ';');
        
        // check for spin syntax
        if(strpos($selected_os, '[') !== FALSE) {
            $selected_os = self::processSpinSyntax($selected_os);
        }
        
        // check for random number syntax
        if(strpos($selected_os, ':number') !== FALSE) {
            $selected_os = self::processRandomNumbers($selected_os);
        }
        
        if(random_int(1, 100) > 50) {
            $selected_os .= '; en-US';
        }
        return $selected_os;
    }
    
    /**
     * Get Mobile OS
     * @param null|string $os Can specifiy android, iphone, ipad, ipod, or null/blank for random
     * @return string *
     */
    public function getMobileOS($os = NULL) {
        $os = strtolower($os);
        $_os = [];
        switch( $os ) {
            case'android':
                $_os += $this->android_os;
            break;
            case 'iphone':
            case 'ipad':
            case 'ipod':
                $_os[] = $this->mobile_ios[$os];
            break;
            default:
                $_os = array_merge($this->android_os, array_values($this->mobile_ios));
        }
        // select random mobile os
        $selected_os = rtrim($_os[random_int(0, count($_os) - 1)], ';');
        if(strpos($selected_os, ':androidVersion:') !== FALSE) {
            $selected_os = $this->processAndroidVersion($selected_os);
        }
        if(strpos($selected_os, ':androidDevice:') !== FALSE) {
            $selected_os = $this->addAndroidDevice($selected_os);
        }
        if(strpos($selected_os, ':number') !== FALSE) {
            $selected_os = self::processRandomNumbers($selected_os);
        }
        return $selected_os;
    }
    
    /**
     *  static::processRandomNumbers
     * @param $selected_os
     * @return null|string|string[] *
     */
    public static function processRandomNumbers($selected_os) {
        return preg_replace_callback('/:number(\d+)-(\d+):/i', function($matches) { return random_int($matches[1], $matches[2]); }, $selected_os);
    }
    
    /**
     *  static::processSpinSyntax
     * @param $selected_os
     * @return null|string|string[] *
     */
    public static function processSpinSyntax($selected_os) {
        return preg_replace_callback('/\[([\w\-\s|;]*?)\]/i', function($matches) {
            $shuffle = explode('|', $matches[1]);
            return $shuffle[array_rand($shuffle)];
        }, $selected_os);
    }
    
    /**
     * processAndroidVersion
     * @param $selected_os
     * @return null|string|string[] *
     */
    public function processAndroidVersion($selected_os) {
        $this->androidVersion = $version = $this->androidVersions[array_rand($this->androidVersions)];
        return preg_replace_callback('/:androidVersion:/i', function($matches) use ($version) { return $version; }, $selected_os);
    }
    
    /**
     * addAndroidDevice
     * @param $selected_os
     * @return null|string|string[] *
     */
    public function addAndroidDevice($selected_os) {
        $devices = $this->androidDevices[substr($this->androidVersion, 0, 3)];
        $device  = $devices[array_rand($devices)];
        
        $device = self::processSpinSyntax($device);
        return preg_replace_callback('/:androidDevice:/i', function($matches) use ($device) { return $device; }, $selected_os);
    }
    
    /**
     *  static::chromeVersion
     * @param $version
     * @return string *
     */
    public static function chromeVersion($version) {
        return random_int($version['min'], $version['max']) . '.0.' . random_int(1000, 4000) . '.' . random_int(100, 400);
    }
    
    /**
     *  static::firefoxVersion
     * @param $version
     * @return string *
     */
    public static function firefoxVersion($version) {
        return random_int($version['min'], $version['max']) . '.' . random_int(0, 9);
    }
    
    /**
     *  static::windows
     * @param $version
     * @return string *
     */
    public static function windows($version) {
        return random_int($version['min'], $version['max']) . '.' . random_int(0, 9);
    }
    
    /**
     * generate
     * @param null $userAgent
     * @return string *
     */
    public function generate($userAgent = NULL) {
        if($userAgent === NULL) {
            $r = random_int(0, 100);
            if($r >= 44) {
                $userAgent = array_rand([ 'firefox' => 1, 'chrome' => 1, 'explorer' => 1 ]);
            } else {
                $userAgent = array_rand([ 'iphone' => 1, 'android' => 1, 'mobile' => 1 ]);
            }
        } elseif($userAgent == 'windows' || $userAgent == 'mac' || $userAgent == 'linux') {
            $agents = [ 'firefox' => 1, 'chrome' => 1 ];
            if($userAgent == 'windows') {
                $agents['explorer'] = 1;
            }
            $userAgent = array_rand($agents);
        }
        $_SESSION['agent'] = $userAgent;
        if($userAgent == 'chrome') {
            return 'Mozilla/5.0 (' . $this->getOS($userAgent) . ') AppleWebKit/' . (random_int(1, 100) > 50 ? random_int(533, 537) : random_int(600, 603)) . '.' . random_int(1, 50) . ' (KHTML, like Gecko) Chrome/' . self::chromeVersion([ 'min' => 47,
                                                                                                                                                                                                                                              'max' => 55 ]) . ' Safari/' . (random_int(1, 100) > 50 ? random_int(533, 537) : random_int(600, 603));
        } elseif($userAgent == 'firefox') {
            
            return 'Mozilla/5.0 (' . $this->getOS($userAgent) . ') Gecko/' . (random_int(1, 100) > 30 ? '20100101' : '20130401') . ' Firefox/' . self::firefoxVersion([ 'min' => 45,
                                                                                                                                                                        'max' => 74 ]);
        } elseif($userAgent == 'explorer') {
            
            return 'Mozilla / 5.0 (compatible; MSIE ' . ($int = random_int(7, 11)) . '.0; ' . $this->getOS('windows') . ' Trident / ' . ($int == 7 || $int == 8 ? '4' : ($int == 9 ? '5' : ($int == 10 ? '6' : '7'))) . '.0)';
        } elseif($userAgent == 'mobile' || $userAgent == 'android' || $userAgent == 'iphone' || $userAgent == 'ipad' || $userAgent == 'ipod') {
            
            return '' . $this->getMobileOS($userAgent) . '';
        } else {
            new Exception('Unable to determine user agent to generate');
        }
    }
}


// $regFile = file_get_contents('lykaregister.json');
// $filesReg = json_decode($regFile, true);
// $cookyStr = file_get_contents('lyaccnts.json');
// $accntdata = json_decode($cookyStr, true);
// function postX($urlx, $payloader, $header){
function postX($urlx){
    global $uAgent;
    $validURL = $urlx;
    // $validHeader = $header;
    $validCurl = curl_init($validURL);
    curl_setopt($validCurl, CURLOPT_URL, $validURL);
    curl_setopt($validCurl, CURLOPT_POST, true);
    curl_setopt($validCurl, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($validCurl, CURLOPT_HTTPHEADER, $validHeader);
    // curl_setopt($validCurl, CURLOPT_POSTFIELDS, $payloader);
    curl_setopt($validCurl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($validCurl, CURLOPT_SSL_VERIFYPEER, false);
    $validResp = curl_exec($validCurl);
    curl_close($validCurl);
    // $valjson = json_decode($validResp);
    return $validResp;
};

// function payload($nameid, $lastcode, $numberstat){
function payload($numberstat){
    if ($numberstat != 'Ok') {
        $register = <<<DATA
            {
                "brand": "prepaid",
                "mobileNumber": "$numberstat",
                "segment": "mobile"
            }
            DATA;
        return $register;
        $otp = <<<DATA
        {
            "first_name": "$nameid",
            "last_name": "$lastcode",
            "password": "cocomelon",
            "source": "android",
            "identity": {
            "type": "msisdn",
            "value": "+63$numberstat"
            },
            "consent": {
            "credit_scoring": false,
            "third_party_advertising": false,
            "advertising": false,
            "profiling": false,
            "profile_sharing": false
            }
        }
        DATA;
        return $otp;
    }else{
        
        $register = <<<DATA
            {
                "registration_id": "$nameid",
                "vcode": "$lastcode"
            }
            DATA;
        return $register;
    }

};

function randomName($param) {
    $firstname = array(
        'Johnathon',
        'Anthony',
        'Erasmo',
        'Raleigh',
        'Nancie',
        'Tama',
        'Camellia',
        'Augustine',
        'Christeen',
        'Luz',
        'Diego',
        'Lyndia',
        'Thomas',
        'Georgianna',
        'Leigha',
        'Alejandro',
        'Marquis',
        'Joan',
        'Stephania',
        'Elroy',
        'Zonia',
        'Buffy',
        'Sharie',
        'Blythe',
        'Gaylene',
        'Elida',
        'Randy',
        'Margarete',
        'Margarett',
        'Dion',
        'Tomi',
        'Arden',
        'Clora',
        'Laine',
        'Becki',
        'Margherita',
        'Bong',
        'Jeanice',
        'Qiana',
        'Lawanda',
        'Rebecka',
        'Maribel',
        'Tami',
        'Yuri',
        'Michele',
        'Rubi',
        'Larisa',
        'Lloyd',
        'Tyisha',
        'Samatha',
        'Cano',
        'Rankin',
        'Mcdonald',
        'Arnold',
        'Joseph',
        'Arias',
        'Dugan',
        'Belcher',
        'Stapleton',
        'Flores',
        'Patton',
        'Grover',
        'Singh',
        'Tapia',
        'Ramirez',
        'Clay',
        'Tillman',
        'Galvan',
        'Anderson',
        'Hale',
        'Hendrickson',
        'Myles',
        'Hope',
        'Woody',
        'Cates',
        'Crum',
        'Harper',
        'Ross',
        'Lott',
        'Bower',
        'Britton',
        'Carmichael',
        'Sanford',
        'Weber',
        'Rowell',
        'Carroll',
        'Francis',
        'Wright',
        'Ochoa',
        'Burt',
        'Franco',
        'Doss',
        'Hammond',
        'Bowden',
        'Plummer',
        'Clement',
        'Dowdy',
        'Ali',
        'Jacobs',
        'Camp',
        'Whitehead',
        'Kurtz',
        'Chapman',
        'Frey',
        'Dubois',
        'Chandler',
        'Fleming',
        'Rouse',
        'Plummer',
        'Logan',
        'Guerrero',
        'Vogt',
        'Mcgee',
        'Arrington',
        'Snow',
        'Toney',
        'Spicer',
        'Brewer',
        'Britt',
        'Lovell',
        'Romano',
        'Shultz',
        'Bliss',
        'Shelton',
        'Bain',
        'Eubanks',
        'Parsons',
        'Nadeau',
        'Courtney',
        'Frye',
        'Harvey',
        'Perkins',
        'Seymour',
        'Cordero',
    );

    $lastname = array(
        'Cano',
        'Rankin',
        'Mcdonald',
        'Arnold',
        'Joseph',
        'Arias',
        'Dugan',
        'Belcher',
        'Stapleton',
        'Flores',
        'Patton',
        'Grover',
        'Singh',
        'Tapia',
        'Ramirez',
        'Clay',
        'Tillman',
        'Galvan',
        'Anderson',
        'Hale',
        'Hendrickson',
        'Myles',
        'Hope',
        'Woody',
        'Cates',
        'Crum',
        'Harper',
        'Ross',
        'Lott',
        'Bower',
        'Britton',
        'Carmichael',
        'Sanford',
        'Weber',
        'Rowell',
        'Carroll',
        'Francis',
        'Wright',
        'Ochoa',
        'Burt',
        'Franco',
        'Doss',
        'Hammond',
        'Bowden',
        'Plummer',
        'Clement',
        'Dowdy',
        'Ali',
        'Jacobs',
        'Camp',
        'Whitehead',
        'Kurtz',
        'Chapman',
        'Frey',
        'Dubois',
        'Chandler',
        'Fleming',
        'Rouse',
        'Plummer',
        'Logan',
        'Guerrero',
        'Vogt',
        'Mcgee',
        'Arrington',
        'Snow',
        'Toney',
        'Spicer',
        'Brewer',
        'Britt',
        'Lovell',
        'Romano',
        'Shultz',
        'Bliss',
        'Shelton',
        'Bain',
        'Eubanks',
        'Parsons',
        'Nadeau',
        'Courtney',
        'Frye',
        'Harvey',
        'Perkins',
        'Seymour',
        'Cordero',
        'Correa',
        'Gallo',
        'Hawkins',
        'Pierson',
        'Sheets',
        'Lynn',
        'Boggs',
        'Looney',
        'Rosado',
        'Hahn',
        'Flores',
        'Griggs',
        'Horton',
        'Francis',
        'Salinas',
        'Mischke',
        'Serna',
        'Pingree',
        'Mcnaught',
        'Pepper',
        'Schildgen',
        'Mongold',
        'Wrona',
        'Geddes',
        'Lanz',
        'Fetzer',
        'Schroeder',
        'Block',
        'Mayoral',
        'Fleishman',
        'Roberie',
        'Latson',
        'Lupo',
        'Motsinger',
        'Drews',
        'Coby',
        'Redner',
        'Culton',
        'Howe',
        'Stoval',
        'Michaud',
        'Mote',
        'Menjivar',
        'Wiers',
        'Paris',
        'Grisby',
        'Noren',
        'Damron',
        'Kazmierczak',
        'Haslett',
        'Guillemette',
        'Buresh',
        'Center',
        'Kucera',
        'Catt',
        'Badon',
        'Grumbles',
        'Antes',
        'Byron',
        'Volkman',
        'Klemp',
        'Pekar',
        'Pecora',
        'Schewe',
        'Ramage',
    );
    // $name = $firstname[rand ( 0 , count($firstname) -1)];
    // $name .= ' ';
    // $name .= $lastname[rand ( 0 , count($lastname) -1)];
    if ($param == "lastname") {
        $name = $lastname[rand ( 0 , count($lastname) -1)];
    }else{
        $name = $firstname[rand ( 0 , count($firstname) -1)];
    }
    return $name;
}
$wh="\033[1;94m";
$lred="\033[1;91m";

function register(){
    $b="\033[1;35m";
    $green="\033[1;32m";
    $yllw="\033[1;33m";
    $wh="\033[1;94m";
    $cyn="\033[1;96m";
    $lred="\033[1;91m";
    $wht="\033[1;97m";
    $blhigh = "\033[44m";
    $defhigh = "\033[49m";
    $lcyan = "\033[34m";
    // global $password;
    global $name;
    global $login;
    global $last;
    global $number;
    global $uAgent;
    $agent = new userAgent();
    $uAgent = $agent->generate('android');
    echo $green;
    echo "\nPaymaya Registration Script";
    echo "\n\n$lred\n ©S$wht"."hA$lred"."D0$wht"."W046$wh v1.0.0";
    echo "\n";
    echo "$cyn Device model and Version : $yllw$uAgent\n";
    $name= randomName('firstname');
    $last= randomName('lastname');
    echo "$lred Generated Info...\n$wht Firstname : $green$name\n$wht Lastname : $green$last\n\n";
    echo $yllw;
    $number= readline("Enter your Phone Number: +63");
    if($number == "x"){
        echo $wht;
        exit('cancelled.'."\n");
    }
    echo $green;
    $sendHeader = array(
        "Host: new.globe.com.ph",
        "Content-Type: application/json",
        "user-agent: $uAgent",
        "authorization: Bearer eyJraWQiOiJyc2ExIiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiJwYXltYXlhLWFuZHJvaWQiLCJhenAiOiJwYXltYXlhLWFuZHJvaWQiLCJzY29wZSI6ImludGVybmFsIG9wZW5fcGxhdGZvcm1fdG9rZW4ud3JpdGUiLCJpc3MiOiJodHRwczpcL1wvY29ubmVjdC5wYXltYXlhLmNvbSIsImV4cCI6MTY1MDE2MDk5MCwiaWF0IjoxNjQ3NTY4OTkxLCJqdGkiOiJjMDk2MGE2NC1iOTE1LTQyNTYtYjg0ZS04NzNlNDk3ZTE4ZDgifQ.Uf9F38hp5hB9SwYanoqNGP_0kQfQZ7Z1yEkMGk8DM2Ub2-Cye_xvsx3VJjcJlrrNS_j6ZQrgrT-EoZFQ0G8GPb4iTdCt9pQ5kIY9NUMBTquf_kxI5dxwVvNJxFlKMtruTt5pkOp6Ogt5wu3brcVKAL-f0M3MeHkPvESqoDP3MFr-6C3_wBQWJBHm5mfrO4Dgf8AYyLqYCXWz-jpCqwbHWIB7PM5QKsz_S-9oey70EP9dOHnMcDabMAdwYLtNhG8w1THyLHBQA8U3jJ3_6e4UK9oy3ispa8Jcn5teugjq4NsgQhYD8EXH6l2CWmRNFlYafBStSTRHNSYSmXF77jhL2Q"
        );
    // $sendOTPs = postX("https://api.paymaya.com/client/v1/accounts/register", payload($name, $last, $number), $sendHeader);
    $sendOTPs = postX("https://simreg.smart.com.ph/api/otp/sendOTP?mobileNumber=9218772910");
    return $sendOTPs;

    $result = json_decode($sendOTPs, true);
    $sendOTP = json_decode($sendOTPs);
    if (isset( $result['registration_id'] )) {
        $regid = $sendOTP->registration_id;
        echo "\n";
        echo $yllw;
        $Otp= readline("-->> OTP: ");
        $headers = array(
            "Host: api.paymaya.com",
            "content-type: application/json",
            "user-agent: $uAgent",
            "session_id: ".$Otp."ae0c6342c488d1da60c9".$Otp,
            "cid: $regid",
            "client_app_version: 2.61.0",
            "client_os_name: android",
            "authorization:Bearer eyJraWQiOiJyc2ExIiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiJwYXltYXlhLWFuZHJvaWQiLCJhenAiOiJwYXltYXlhLWFuZHJvaWQiLCJzY29wZSI6ImludGVybmFsIG9wZW5fcGxhdGZvcm1fdG9rZW4ud3JpdGUiLCJpc3MiOiJodHRwczpcL1wvY29ubmVjdC5wYXltYXlhLmNvbSIsImV4cCI6MTY1MDE2MDk5MCwiaWF0IjoxNjQ3NTY4OTkxLCJqdGkiOiJjMDk2MGE2NC1iOTE1LTQyNTYtYjg0ZS04NzNlNDk3ZTE4ZDgifQ.Uf9F38hp5hB9SwYanoqNGP_0kQfQZ7Z1yEkMGk8DM2Ub2-Cye_xvsx3VJjcJlrrNS_j6ZQrgrT-EoZFQ0G8GPb4iTdCt9pQ5kIY9NUMBTquf_kxI5dxwVvNJxFlKMtruTt5pkOp6Ogt5wu3brcVKAL-f0M3MeHkPvESqoDP3MFr-6C3_wBQWJBHm5mfrO4Dgf8AYyLqYCXWz-jpCqwbHWIB7PM5QKsz_S-9oey70EP9dOHnMcDabMAdwYLtNhG8w1THyLHBQA8U3jJ3_6e4UK9oy3ispa8Jcn5teugjq4NsgQhYD8EXH6l2CWmRNFlYafBStSTRHNSYSmXF77jhL2Q",
        );
        $registers = postX("https://api.paymaya.com/client/v3/accounts/verify", payload($regid, $Otp, 'Ok'), $headers);
        $register = json_decode($registers);
        file_put_contents('final.json', $registers);
        // $token = $register->type->token;
        $results = json_decode($registers, true);
        if (isset( $results['error'] )) {
            echo $lred;
            if ($register->error->code == "-21"){
                $xresult = "Incorrect Verification Code.";
            }elseif ($register->error->code == "-22"){
                $xresult = "Expired Verification Code.";
            }elseif ($register->error->code == "-31"){
                $xresult = $register->error->spiel;
            }elseif ($register == "cid is required and must be a valid uuid."){
                $xresult = "Incorrect Registration ID.";
            }elseif ($register->error->code == "-262"){
                $xresult = $register->error->spiel;
            }elseif ($register->error->code == "-1"){
                $xresult = ''.$register->error->spiel.'';
            }elseif ($register->error->code == "-5"){
                $xresult = ''.$register->error->spiel.'';
            }
        }else{
            echo $green;
            $xresult = 'Account Status : '.$register->account_status.'\n Account Name : '.$register->profile->first_name.' '.$register->profile->last_name.'\nPassword: cocomelon';
        }
        echo "\n".$xresult."\n";
    }else{
        if ($sendOTP->error->code == "-12"){
            $result = "Number is already registered.";
        }elseif ($sendOTP->error->code == "-1"){
            $result = ''.$error.'';
        }
        echo $result."\n";
        echo $wht;
        exit('cancelled.'."\n");
    }
    register();
}
register();
?>