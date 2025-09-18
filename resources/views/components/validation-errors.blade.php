@if ($errors->any())
    <div style="
        color: white;
        font-size: 14px;
        margin-bottom: 10px;
        background: red;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid red;
        text-align: center;
        width: 100%;">
        
        <ul style="list-style: none; padding: 0; margin: 0;">
            @foreach ($errors->all() as $error)
                <li> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
