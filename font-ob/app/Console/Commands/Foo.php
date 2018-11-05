<?php

namespace FontObscure\Console\Commands;

use Illuminate\Console\Command;

use Storage;

class Foo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'font:foo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'foo Command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // dd(pack('CCC', 0x30,0x31,0x32));
        echo 'Hey Hey !'."\n";
// $h = pack('J', 0x00010000);
// dd($h);

// var_dump(
//     Storage::disk('local')->get('strokes/test.png')
// );

// dd(
//     pack('C', 1)
// );
        $this->foo();
    }

    protected function foo()
    {
        $file = Storage::disk('local')->get('strokes/font.ttf');

        $h = unpack('Nver/nnum/nrange/nselector/nshift', $file);

echo '-----------------------------------------------------------'."\n";
dump('header');
dump($h);
echo "\n\n\n";

        $tableRecords = [];
        $readOffset = 12;
        for ($i = 0; $i < $h['num']; $i++) {
            $tag = substr($file, $readOffset, 4);
            $tableRecordData = substr($file, ($readOffset + 4), (16 - 4));

            $t = unpack('Nsum/Noffset/Nlength', $tableRecordData);

            $tableRecords[$tag] = $t;
            $readOffset += 16;
        }

        dump($tableRecords);


//         foreach ($tableRecords as $key => $t) {
//             $test = substr($f, $t['offset'], $t['length']);
//             $mySum = $this->calculateCheckSum($test);
// dump( sprintf('calculated-sum=%08x , org-sum=%08x', $mySum, $t['sum']) );
//         }

dd('OK');
    }

    protected function x()
    {


// $a = $this->calculateCheckSum(pack('NN', 0x013C , 0x001C));
// $a = sprintf('%08x', 0xB1B0AFBA - $a);
// dd($a);

        // $svgText = Storage::disk('local')->get('strokes/a.svg');
        // $a = simplexml_load_string($svgText);

        $bin = pack('N', 0x00010000);  //sfntVersion

        $bin .= pack('n', 1);  //numTables
        $bin .= pack('n', 16);  //searchRange
        $bin .= pack('n', 0);  //entrySelector
        $bin .= pack('n', 0);  //rangeShift


        //
        $bin .= $this->getTableRecord('cmap', 1, 1);




        $r = Storage::disk('local')->put('strokes/bin.txt', $bin);



        // dd($a);
        return true;
    }

    protected function getTableRecord($tag, int $offset, int $length)
    {
        $content = pack('N', $offset).pack('N', $length);
        $sum = $this->calculateCheckSum($content);

        return $tag.pack('N', $sum).$content;
    }

    protected function calculateCheckSum(string $table)
    {
        // 0xB1B0AFBA ?
        $length = ((strlen($table) + 3) & ~3) / 4;
// echo 'r-len = '.strlen($table)."\n";
// echo 'p-len = '.($length * 4)."\n";
        $bin = unpack("N{$length}", $table);
        $sum = 0;
        foreach ($bin as $p) {
            $sum = ($sum + $p) & 0xffffffff;
        }
        return $sum;
    }
}
