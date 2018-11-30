<?php use OurScene\Helpers\DatetimeUtils; ?>

Hi, {{ $venue->name }}!

<br/><br/><br/><br/>

We're just informing you that {{ $artist->name }} cancelled the following request for performance.

<br/><br/><br/>

<span style="color: #534d93; font-weight: bold;">Event details</span>

<br/><br/>

<b>Title</b><br/>
{{ $event->title }}

<br/><br/>

<b>Start</b><br/>
{{ date('F d, Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($event['start_datetime'])->sec) }}

<br/><br/>

<b>End</b><br/>
{{ date('F d, Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($event['end_datetime'])->sec) }}

<br/><br/>

<span style="color: #534d93; font-weight: bold;">Performance time</span>

<br/><br/>

<b>Start</b><br/>
{{ date('F d, Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service['start_datetime'])->sec) }}

<br/><br/>

<b>End</b><br/>
{{ date('F d, Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service['end_datetime'])->sec) }}

<br/><br/><br/>

You can <a href="{{ action('UserController@getLogin') }}" style="color: #534d93; text-decoration: none;">log in</a> to check if you have more incoming requests.

<br/><br/><br/><br/>

Cheers,

<br/><br/><br/>

OurScene