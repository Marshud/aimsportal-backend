<x-mail::message>
# User requests to join organisation

Dear {{$newUser->currentOrganisation->name}} Member,

You can approve or reject the user from joining your organisation. their details are below.
<x-mail::table>
| Name       | Email         | Organisation to Join  |
| ------------- |:-------------:| --------:|
| {{$newUser->name}}      | {{$newUser->email}}      | {{$newUser->currentOrganisation->name}}      |

</x-mail::table>

<x-mail::button :url="$frontEndUrl">
Approve
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
