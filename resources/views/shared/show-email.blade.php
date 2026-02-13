{{--
/* ----------------------------------------------------------------------------
 * Timecrack - Simple Bookmark Manager
 *
 * @package     Timecrack
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://github.com/alextselegidis/timecrack
 * ---------------------------------------------------------------------------- */
--}}

<div class="mb-4">
    <h6 class="text-muted">
        {{$label}}
    </h6>
    @if($value)
        <a href="mailto:{{$value}}">{{$value}}</a>
    @else
        -
    @endif
</div>
