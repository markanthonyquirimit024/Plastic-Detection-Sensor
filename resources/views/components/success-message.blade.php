@if (session('message'))
    <div 
        {{ $attributes->merge(['class' => 'success-message']) }}
        style="color: green;
               font-size: 14px;
               margin-bottom: 10px;
               background: rgba(0, 255, 0, 0.2);
               padding: 10px;
               border-radius: 5px;
               border: 1px solid green;
               text-align: center;
               width: 90%;">
        {{ session('message') }}
    </div>
@endif
