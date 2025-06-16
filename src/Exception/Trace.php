<?php

namespace DohFormatting\Doh\Exception;

/**
 * Represents a list of stack trace frames.
 *
 * Provides convenience methods to access, filter, and iterate over the stack trace,
 * including determining the most relevant frame for error reporting.
 *
 * @implements \IteratorAggregate<int, Frame>
 */
class Trace implements \IteratorAggregate
{
    /**
     * @var Frame[] List of frames in the trace.
     */
    private array $frames = [];

    /**
     * @param Frame[] $frames Optional array of Frame instances to initialize the trace.
     */
    public function __construct(array $frames = [])
    {
        foreach ($frames as $frame) {
            $this->append($frame);
        }
    }

    /**
     * Adds a frame to the end of the trace.
     *
     * @param Frame $frame
     */
    public function append(Frame $frame): void
    {
        $this->frames[] = $frame;
    }

    /**
     * Adds a frame to the beginning of the trace.
     *
     * @param Frame $frame
     */
    public function prepend(Frame $frame): void
    {
        array_unshift($this->frames, $frame);
    }

    /**
     * Returns all frames in the trace.
     *
     * @return Frame[]
     */
    public function all(): array
    {
        return $this->frames;
    }

    /**
     * Returns an iterator for the frames.
     *
     * @return \Traversable<int, Frame>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->frames);
    }

    /**
     * Returns the most relevant frame for debugging purposes.
     * Skips internal and vendor frames if possible.
     *
     * @return Frame|null
     */
    public function mostRelevant(): ?Frame
    {
        return $this->frames[$this->mostRelevantIndex()] ?? null;
    }

    /**
     * Returns the index of the most relevant frame.
     * Prefers app code over vendor/internal frames.
     *
     * @return int
     */
    public function mostRelevantIndex(): int
    {
        foreach ($this->frames as $i => $frame) {
            if (!$frame->isInternal() && !$frame->isVendor() && $i != 0) {
                return $i;
            }
        }

        return 0;
    }

    /**
     * Filters and returns only frames that belong to the app (not vendor/internal).
     *
     * @return Frame[]
     */
    public function onlyApp(): array
    {
        return array_filter($this->frames, fn(Frame $f) => $f->isApp());
    }

    /**
     * Converts the trace to a plain array structure suitable for serialization or inspection.
     *
     * @return array<int, array<string, mixed>>
     */
    public function toArray(): array
    {
        return array_map(fn(Frame $f) => [
            'file' => $f->file,
            'line' => $f->line,
            'function' => $f->function,
            'class' => $f->class,
            'type' => $f->type,
            'args' => $f->args,
        ], $this->frames);
    }

    /**
     * Creates a Trace from a raw debug_backtrace array and optional virtual frames.
     *
     * @param array<int, array<string, mixed>> $backtrace
     * @param Frame[] $virtualFrames
     * @return self
     */
    public static function fromBacktrace(array $backtrace, array $virtualFrames = []): self
    {
        $frames = array_map(
            fn(array $f): Frame => Frame::fromArray($f),
            $backtrace
        );

        $trace = new self($frames);

        foreach (array_reverse($virtualFrames) as $frame) {
            $trace->prepend($frame);
        }

        return $trace;
    }
}