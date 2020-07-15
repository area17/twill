<!DOCTYPE html>
<html>
<head>
    <title>Twill icons</title>
</head>
<style>
    body {
      margin: 0 auto;
      font-family: system-ui;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      grid-auto-rows: minmax(150px, auto);
      grid-gap: 10px;
      background-color: #eee;
    }

    .icon {
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 150px;
    }

    .icon__inner {
        display:flex;
        flex-direction:column;
    }

    .icon__inner img {
        padding-bottom: 20px;
        height: 16px;
    }
</style>
<body>
    <div class="grid">
        @foreach($icons as $icon)
            <div class="icon">
                <div class="icon__inner">
                    <img src="{{ $icon['url'] }}" />
                    <span>{{ $icon['name'] }}</span>
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>
