@extends ('layouts.app')
@section('head')
<link href="{{ asset('libs/floating-whatsapp/floating-wpp.min.css') }}" rel="stylesheet">
@endsection
@section ('content')
    @if($row->template_id)
        <div class="page-template-content">
            {!! $row->getProcessedContent() !!}
        </div>
    @else
        <div class="container " style="padding-top: 40px;padding-bottom: 40px;">
            <h1>{{$translation->title}}</h1>
            <div class="blog-content">
                {!! $translation->content !!}
            </div>
        </div>
    @endif
    <div id="bravo-whatsapp-widget"></div>  
@endsection

@section('footer')
<script src="{{ asset('libs/floating-whatsapp/floating-wpp.min.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $('#bravo-whatsapp-widget').floatingWhatsApp({
            phone: '03464707610',
            popupMessage: 'Hi, how can we help you?',
            message: "I'd like to know more about your services.",
            showPopup: false,
            showOnIE: false,
            autoOpenTimeout: 0,
            headerTitle: 'Welcome!',
            headerColor: '#a80505',
            backgroundColor: 'white',
            position: 'right',            
            buttonImage: '<img src="{{ asset('images/whatsapp.svg') }}" />'
        });
      });

</script>

@endsection