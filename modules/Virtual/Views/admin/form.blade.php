<div class="form-group">
    <label>{{__("Location Name")}}</label>
    <input type="text" value="{{$translation->name}}" placeholder="{{__("Location name")}}" name="name" class="form-control">
</div>
<div class="form-group">
    <label>{{__("Video Link")}}</label>
    <input type="text" value="{{$translation->name}}" placeholder="{{__("Video link")}}" name="voice" class="form-control">
</div>
<div class="form-group">
    <label>{{__("Voice Link")}}</label>
    <input type="text" value="{{$translation->name}}" placeholder="{{__("Voice link")}}" name="video" class="form-control">
</div>

<div class="form-group">
    <label class="control-label">{{__("Description")}}</label>
    <div class="">
        <textarea name="content" class="d-none has-ckeditor" cols="30" rows="10">{{$translation->content}}</textarea>
    </div>
</div>