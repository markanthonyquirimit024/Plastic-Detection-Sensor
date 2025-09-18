@if (session('success'))
    <div 
        {{ $attributes->merge(['class' => 'success-success']) }}
        style="color: white;
               font-size: 14px;
               margin-bottom: 10px;
               background: green;
               padding: 10px;
               border-radius: 5px;
               border: 1px solid green;
               text-align: center;
               width: 100%;">
         {{ session('success') }}
    </div>
@endif