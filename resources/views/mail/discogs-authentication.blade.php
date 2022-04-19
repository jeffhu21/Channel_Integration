@component('mail::message')
# Discogs Authentication

Thank you for using Discogs Channel. 

Please click the button below to authorize

@component('mail::button', ['url' => $url])
Authorize
@endcomponent

Thanks,<br>
{{ 'Linnworks Discogs Integration' }}
@endcomponent
