@component('mail::message')
# Discogs Authentication

Thank you for using Discogs Channel. 

Please click the button below to complete the Discogs Authentication

@component('mail::button', ['url' => $url])
Authentication
@endcomponent

Thanks,<br>
{{ 'Linnworks Discogs Integration' }}
@endcomponent
