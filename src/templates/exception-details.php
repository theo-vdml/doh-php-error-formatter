<?php
/**
 * ---------------------------------------------------------------
 * exception-details.php — HTML Error Debugging Template
 * ---------------------------------------------------------------
 * This template is used by the Doh error handler to render a 
 * user-friendly, developer-focused HTML page when an exception 
 * or error is caught. It provides contextual information about 
 * the throwable and its stack trace, including source code excerpts.
 *
 * Variables available in this template:
 *
 * @var \Throwable $t
 *      The original exception or error that was thrown. Provides 
 *      methods like getMessage(), getFile(), getLine(), getTrace(), etc.
 *
 * @var \DohFormatting\Doh\Exception\Trace $trace
 *      A structured and enhanced representation of the throwable's 
 *      stack trace, wrapped in a Trace object. Contains a list of 
 *      Frame objects for each call in the stack.
 *
 * @var string $class
 *      The fully-qualified class name of the throwable (e.g. ErrorException, 
 *      RuntimeException, etc.), already HTML-escaped for output.
 *
 * @var string $message
 *      The throwable’s message, also already escaped to prevent HTML injection.
 * 
 * @var int $relevant
 *      The index of the most relevant frame in the trace, typically the first
 *      non-internal, non-vendor code location where the error originated.
 * @var string[] $highlights
 */
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $class ?>: <?= $message ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/stackoverflow-dark.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />

    <style></style>
    <style>
        <?= file_get_contents(__DIR__ . '/style.css') ?>
    </style>
    <script>
        <?= file_get_contents(__DIR__ . '/script.js') ?>
    </script>
</head>

<body class="bg-zinc-900 overflow-y-scroll">
    <div>

        <div class="max-w-6xl mt-16 m-auto">
            <h1 class="text-5xl text-white font-extrabold flex items-center gap-8">
                <i class="ri-error-warning-line bg-red-800 text-red-200 rounded-full p-4 text-4xl"></i>
                Unhandled Exception
            </h1>
        </div>

        <div class="max-w-6xl mt-16 m-auto space-y-2 bg-zinc-800 rounded-lg p-8">
            <div class="text-red-100 text-base font-semibold px-4 py-2 bg-red-800 rounded-3xl w-fit">
                <?= htmlspecialchars($class) ?>
            </div>
            <h2 class="text-4xl pl-1 text-white font-bold"><?= htmlspecialchars($message) ?></h2>
        </div>

        <div class="max-w-6xl my-16 m-auto text-white bg-zinc-800 rounded-lg overflow-hidden">
            <div class="p-8 bg-red-900">
                <h3 class="text-3xl font-bold"><i class="ri-stacked-view mr-4"></i>Stack Frames
                    (<?= count($trace->all()) ?>)</h3>
            </div>
            <div class="p-4 space-y-2">
                <?php foreach ($trace as $index => $frame): ?>
                    <?php $open = $index === $relevant ?>
                    <div class="accordion">
                        <button type="button"
                            class="w-full flex justify-between items-center bg-zinc-900 hover:bg-zinc-950 p-4 py-3 rounded-t focus:outline-none  <?= $open ? '' : 'rounded-b' ?>"
                            aria-expanded="<?= $open ? 'true' : 'false' ?>" aria-controls="panel-<?= $index ?>"
                            id="accordion-<?= $index ?>">
                            <p>
                                <span class="text-zinc-400 font-bold mx-2">#<?= count($trace->all()) - 1 - $index ?></span>
                                <span class="text-zinc-200" title="<?= htmlspecialchars($frame->getFilePath()) ?>">
                                    <?= htmlspecialchars($frame->getFileLabel()) ?>
                                </span>
                                <span class="text-zinc-400 font-bold mx-2">—</span>
                                <span class="font-semibold font-mono"><?= $frame->getContext() ?></span>
                                <span class="text-zinc-400">
                                    <?= $frame->getLineText() ?>
                                </span>
                                <?php if ($index === 0): ?>
                                    <span
                                        class="text-red-200 text-xs ml-2 bg-red-800 px-2 py-1 rounded font-semibold select-none">Exception
                                        Point</span>
                                <?php elseif ($frame->isInternal()): ?>
                                    <span
                                        class="text-blue-200 text-xs ml-2 bg-blue-800 px-2 py-1 rounded font-semibold select-none">Internal
                                        Call</span>
                                <?php elseif ($frame->isVendor()): ?>
                                    <span
                                        class="text-orange-200 text-xs ml-2 bg-orange-800 px-2 py-1 rounded font-semibold select-none">Vendor
                                        Code</span>
                                <?php elseif ($frame->isApp()): ?>
                                    <span
                                        class="text-green-200 text-xs ml-2 bg-green-800 px-2 py-1 rounded font-semibold select-none">Application</span>
                                <?php endif; ?>

                            </p>
                            <i class="ri-arrow-drop-down-line text-2xl"></i>
                        </button>

                        <div id="panel-<?= $index ?>" role="region" aria-labelledby="accordion-<?= $index ?>"
                            class="overflow-hidden transition-max-height duration-300 ease-in-out <?= $open ? 'max-h-screen' : 'max-h-0' ?>">
                            <?php if ($highlights[$index]): ?>
                                <p class="bg-zinc-900 p-4 text-zinc-400"><?= $frame->getFilePath() ?></p>
                                <?= $highlights[$index] ?>
                            <?php else: ?>
                                <div
                                    class="bg-orange-900 bg-opacity-30 text-orange-100 p-6 rounded-b-md text-center select-none font-medium">
                                    <i class="ri-file-lock-line text-2xl"></i> Source code not available for internal calls.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>

</html>