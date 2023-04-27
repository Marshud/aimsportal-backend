<x-mail::message>
# Reset Password

Dear {{$currentUser->name}} Member,

You have requested a password reset. click link below to reset password
<x-mail::table>
| Name       | Email         | Organisation to Join  |
| ------------- |:-------------:| --------:|
| {{$newUser->name}}      | {{$newUser->email}}      | {{$newUser->currentOrganisation->name}}      |

</x-mail::table>


Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
