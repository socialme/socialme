<?php

// Audio CAPTCHAs
// Based off of SecurImage, LGPLed

$audio_dir = getcwd() . "/audio_cap";

mt_srand(microtime() + time());
mt_srand(mt_rand());

function _randstr($len=6) {
		 $sr = "";
		 for($i=0;$i<$len;$i++){
		 $sr .= chr(mt_rand(97, 122));
		 }
		 return $sr;
}

function generateWAV($letters)
{
		$data_len    = 0;
		$files       = array();
		$out_data    = '';

		foreach ($letters as $letter) {
			$filename = $audio_path . strtoupper($letter) . '.wav';

			$fp = fopen($filename, 'rb');

			$file = array();

			$data = fread($fp, filesize($filename)); // read file in

			$header = substr($data, 0, 36);
			$body   = substr($data, 44);


			$data = unpack('NChunkID/VChunkSize/NFormat/NSubChunk1ID/VSubChunk1Size/vAudioFormat/vNumChannels/VSampleRate/VByteRate/vBlockAlign/vBitsPerSample', $header);

			$file['sub_chunk1_id']   = $data['SubChunk1ID'];
			$file['bits_per_sample'] = $data['BitsPerSample'];
			$file['channels']        = $data['NumChannels'];
			$file['format']          = $data['AudioFormat'];
			$file['sample_rate']     = $data['SampleRate'];
			$file['size']            = $data['ChunkSize'] + 8;
			$file['data']            = $body;

			if ( ($p = strpos($file['data'], 'LIST')) !== false) {
				// If the LIST data is not at the end of the file, this will probably break your sound file
				$info         = substr($file['data'], $p + 4, 8);
				$data         = unpack('Vlength/Vjunk', $info);
				$file['data'] = substr($file['data'], 0, $p);
				$file['size'] = $file['size'] - (strlen($file['data']) - $p);
			}

			$files[] = $file;
			$data    = null;
			$header  = null;
			$body    = null;

			$data_len += strlen($file['data']);

			fclose($fp);
		}

		$out_data = '';
		for($i = 0; $i < sizeof($files); ++$i) {
			if ($i == 0) { // output header
				$out_data .= pack('C4VC8', ord('R'), ord('I'), ord('F'), ord('F'), $data_len + 36, ord('W'), ord('A'), ord('V'), ord('E'), ord('f'), ord('m'), ord('t'), ord(' '));

				$out_data .= pack('VvvVVvv',
				16,
				$files[$i]['format'],
				$files[$i]['channels'],
				$files[$i]['sample_rate'],
				$files[$i]['sample_rate'] * (($files[$i]['bits_per_sample'] * $files[$i]['channels']) / 8),
				($files[$i]['bits_per_sample'] * $files[$i]['channels']) / 8,
				$files[$i]['bits_per_sample'] );

				$out_data .= pack('C4', ord('d'), ord('a'), ord('t'), ord('a'));

				$out_data .= pack('V', $data_len);
			}

			$out_data .= $files[$i]['data'];
		}

		$this->scrambleAudioData($out_data, 'wav');
		return $out_data;
}

function scrambleAudioData(&$data, $format)
{
		if ($format == 'wav') {
			$start = strpos($data, 'data') + 4; // look for "data" indicator
			if ($start === false) $start = 44;  // if not found assume 44 byte header
		} else { // mp3
			$start = 4; // 4 byte (32 bit) frame header
		}
		 
		$start  += mt_rand(1, 64); // randomize starting offset
		$datalen = strlen($data) - $start - 256; // leave last 256 bytes unchanged
		 
		for ($i = $start; $i < $datalen; $i += 64) {
			$ch = ord($data{$i});
			if ($ch < 9 || $ch > 119) continue;

			$data{$i} = chr($ch + mt_rand(-8, 8));
		}
}

header('Content-type: audio/x-wav');
$txt = _randstr(mt_rand(3, 9));
$wav = generateWAV($txt);
session_start();
$_SESSION['vercode'] = $txt;
header('Content-Length: ' . strlen($wav);
echo $wav;

?>