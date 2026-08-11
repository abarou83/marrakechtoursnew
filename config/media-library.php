<?php

return [
    /*
     * The disk on which to store added files and derived images by default.
     */
    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
     * The maximum file size of an item in bytes.
     * 10MB = 10 * 1024 * 1024
     */
    'max_file_size' => 1024 * 1024 * 10,

    /*
     * This queue connection will be used to generate derived and responsive images.
     */
    'queue_connection_name' => env('QUEUE_CONNECTION', 'sync'),

    /*
     * This queue will be used to generate derived and responsive images.
     */
    'queue_name' => 'media-library',

    /*
     * By default all conversions will be performed on a queue.
     */
    'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', true),

    /*
     * The fully qualified class name of the media model.
     */
    'media_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /*
     * When enabled, media collections will be serialised using the default
     * temporary signed route.
     */
    'use_default_collection_serialization' => false,

    /*
     * The class that contains the strategy for determining a media file's path.
     */
    'path_generator' => Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,

    /*
     * The class that contains the strategy for determining how to remove files.
     */
    'file_remover_class' => Spatie\MediaLibrary\Support\FileRemover\DefaultFileRemover::class,

    /*
     * When urls to files get generated, this class will be called.
     */
    'url_generator' => Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,

    /*
     * Whether to activate versioning when urls to files get generated.
     */
    'version_urls' => true,

    /*
     * The media library will try to optimize all converted images by removing
     * metadata and applying a little bit of compression.
     */
    'image_optimizers' => [
        Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
            '-m85',
            '--strip-all',
            '--all-progressive',
        ],

        Spatie\ImageOptimizer\Optimizers\Pngquant::class => [
            '--force',
        ],

        Spatie\ImageOptimizer\Optimizers\Optipng::class => [
            '-i0',
            '-o2',
            '-quiet',
        ],

        Spatie\ImageOptimizer\Optimizers\Svgo::class => [
            '--disable=cleanupIDs',
        ],

        Spatie\ImageOptimizer\Optimizers\Gifsicle::class => [
            '-b',
            '-O3',
        ],

        Spatie\ImageOptimizer\Optimizers\Cwebp::class => [
            '-m 6',
            '-pass 10',
            '-mt',
            '-q 85',
        ],

        Spatie\ImageOptimizer\Optimizers\Avifenc::class => [
            '-a cq-level=23',
            '-j all',
            '--min 0',
            '--max 63',
            '--minalpha 0',
            '--maxalpha 63',
            '-a end-usage=q',
            '-a tune=ssim',
        ],
    ],

    /*
     * These generators will be used to create an image of media files.
     */
    'image_generators' => [
        Spatie\MediaLibrary\Conversions\ImageGenerators\Image::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Webp::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Avif::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Pdf::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Svg::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Video::class,
    ],

    /*
     * The path where to store temporary files while performing image conversions.
     */
    'temporary_directory_path' => storage_path('media-library/temp'),

    /*
     * The engine that should perform the image conversions.
     * Should be either `gd` or `imagick`.
     */
    'image_driver' => env('IMAGE_DRIVER', 'gd'),

    /*
     * FFMPEG & FFProbe binaries paths, used to generate video thumbnails
     */
    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),

    /*
     * The maximum memory usage in bytes while performing image conversions.
     */
    'max_memory_usage' => 512 * 1024 * 1024, // 512MB

    /*
     * The maximum duration in seconds for an image conversion.
     */
    'max_execution_time' => 60 * 5, // 5 minutes

    /*
     * Here you can override the class names of the jobs used by this package.
     */
    'jobs' => [
        'perform_conversions' => Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
        'generate_responsive_images' => Spatie\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob::class,
    ],

    /*
     * When using the addMediaFromUrl method you may want to replace the default downloader.
     */
    'media_downloader' => Spatie\MediaLibrary\Downloaders\DefaultDownloader::class,

    /*
     * When using the addMediaFromUrl method the download will be run with this User-Agent header.
     */
    'downloader_user_agent' => 'Spatie MediaLibrary',

    /*
     * When converting media, the server might use too much memory.
     * This option is used to determine the maximum amount of memory
     * that should be used. Can be null or the memory limit in MB.
     */
    'convert_memory_limit' => 512,

    /*
     * Time in seconds that media items will be cached.
     */
    'remote_cache_ttl' => 3600,

    /*
     * When converting media, the server might timeout.
     */
    'generate_conversions_in_chunks_of' => 5,

    /*
     * Prefix for model type in polymorphic relationship
     */
    'media_model_morph_prefix' => null,

    /*
     * Force lazy loading prevention on the media model.
     */
    'force_lazy_loading' => env('APP_ENV') !== 'production',
];
