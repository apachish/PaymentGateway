<x-guest-layout>
<main class="mainContainer">
    <div class="mainTCRContainer failed">
        <div class="registerMsgContainer failed">
            <img src="/img/Register-failed.svg" alt="Register Successful" />
            <p>خطا:  {{$code}}</p>
        </div>
        <p>{{$message}}</p>
    </div>
    <a href="{{url()->previous()}}" class="ReturnToRegisterPara"> بازگشت  </a>
</main>
</x-guest-layout>
