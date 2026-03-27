<x-mail::message>
# Project Workflow Update

Hello {{ $recipientName }},

Your project workflow has a new update.

<x-mail::panel>
Project: {{ $projectTitle }}

Status: {{ $statusLabel }}

Timestamp: {{ $timestamp }}
</x-mail::panel>

{{ $bodyText }}

@if ($reason)
<x-mail::panel>
Reason: {{ $reason }}
</x-mail::panel>
@endif

<x-mail::button :url="$dashboardUrl">
Open Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
