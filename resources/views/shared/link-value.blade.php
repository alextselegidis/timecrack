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

@if($value)
    <a href="{{$href ?? $value}}" >
        {{$value}}
    </a>
@else
    -
@endif
