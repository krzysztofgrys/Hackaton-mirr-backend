<?php

namespace App\Jobs;

use AWS;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\Models\Media;
use Storage;
use Str;

class AddAltToMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $media;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = $this->media->getUrl();
        $path = str_replace('storage', 'public', $url);
        $rekognition = AWS::createClient('rekognition');
        $result = $rekognition->detectLabels(['Image' => ['Bytes' => Storage::get($path)]]);
        $labels = collect($result->get('Labels'));
        $labels = $labels->take(5)->map(function (array $label) {
            return $label["Name"];
        });

        $translateClient = AWS::createClient('translate');
        $translatedResult = $translateClient->translateText(['SourceLanguageCode' => 'en', 'TargetLanguageCode' => 'pl', 'Text' => $labels->implode(", ")]);
        $translatedLabels = $translatedResult->get('TranslatedText');
        $this->media->setCustomProperty('alt', $translatedLabels);
        $this->media->save();
    }
}
