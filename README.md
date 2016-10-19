PHP M3u class
=============

M3u playlists manipulation (parse & create) class.

Usage
-----
```php
$playlist =
'#EXTM3U

#EXTINF:0,TV Channel
#EXTGRP:News
http://broadcaster.tv/channel.m3u8

#EXTINF:0 group-title="Sports",Sports channel
http://broadcaster.tv/sports.m3u8

# That\'s my favourite song 
#EXTINF:253,John Doe - Universe
/home/jd - universe.mp3
';

// create a playlist object
$m3u = new M3u();

// parse an existing file
$m3u->parse($playlist);

// walk through items
$entries = $m3u->getEntries();
foreach($entries as &$entry) {
    echo $entry->name, PHP_EOL;
}

// add records
$new = new M3uEntry();
$new->path = '/home/test1.mp3';
$m3u->addEntry($new);

// or this way
$m3u->addEntry(new M3uEntry([
    'path' => '/home/test2.mp3',
    'artist' => 'Unknown artist',
    'name' => 'Unknown record'
]));

// export valid m3u playlist
echo $m3u;
```