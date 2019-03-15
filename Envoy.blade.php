@servers(['web' => ['localhost' => '127.0.0.1']])

@setup
    $now = new DateTime();
    $to_dir = basename(__DIR__);
    $new_dir = $now->format('YmdHis');

    $git_hub = 'git@github.com:zvonarev-fitron/fitron.club.git';
    $site = 'fitron.club';

    $home = '/home/fitron/';
    $www = '/home/fitron/www/';

    $release = $www . $site . '/' . $new_dir;
    $current = $www . $site . '/' . $to_dir;
    $path = $www . $site;
    $link = $home . $site;

    $db_password = 'qazWSXedc';
    $db_user = 'fitron';
    $db_tablespace = 'fitron_table';
    $db_name = 'fitron_db';
    $db_schema = 'site';
@endsetup

@story('deploy')
    dump
    git
    clone
    db
    composer
    artisan
    npm
    symlink
    copy
@endstory

@task('git', ['on' => 'web'])
    cd {{ $current }}
    git add -A -v
    git commit -m "commit new dir - {{ $new_dir }}"
    git push -u origin master
@endtask

@task('dump', ['on' => 'web'])
    cd {{ $current }}
    PGPASSWORD="{{ $db_password }}" pg_dump -U {{ $db_user }} -n {{ $db_schema }} {{ $db_name }}_{{ $to_dir }} > {{ $db_name }}_{{ $new_dir }}.sql
@endtask

@task('clone', ['on' => 'web'])
    cd {{ $path }}
    git clone --depth 1 -b master {{ $git_hub }} {{ $new_dir }}
@endtask

@task('db', ['on' => 'web'])
    cd {{ $release }}
    PGPASSWORD="{{ $db_password }}" createdb {{ $db_name }}_{{ $new_dir }} -D {{ $db_tablespace }} -U {{ $db_user }}
    PGPASSWORD="{{ $db_password }}" psql --single-transaction -U {{ $db_user }} {{ $db_name }}_{{ $new_dir }} < {{ $db_name }}_{{ $new_dir }}.sql
    {{--PGPASSWORD="{{ $db_password }}" pg_restore -U {{ $db_user }} -c -d {{ $db_name }}_{{ $new_dir }}  {{ $db_name }}_{{ $new_dir }}.dump--}}
@endtask

@task('composer', ['on' => 'web'])
    cd {{ $release }}
    composer install
@endtask

@task('npm', ['on' => 'web'])
    cd {{ $release }}
    npm install --prefer-dist
@endtask

@task('symlink', ['on' => 'web'])
    cd {{ $home }}
    ln -nfs {{ $release }} {{ $link }}
@endtask

@task('artisan', ['on' => 'web'])
    cd {{ $release }}
    php artisan config:clear
    cp .env.example .env
    php artisan key:generate
    php artisan env:set DB_CONNECTION=pgsql
    php artisan env:set DB_HOST=127.0.0.1
    php artisan env:set DB_PORT=5432
    php artisan env:set DB_DATABASE={{ $db_name }}_{{ $new_dir }}
    php artisan env:set DB_USERNAME={{ $db_user }}
    php artisan env:set DB_PASSWORD={{ $db_password }}
    php artisan env:set APP_URL={{ 'http://' . $site }}
    php artisan env:set NOCAPTCHA_SECRET=6LclVH8UAAAAAIpO8isyOiyZ0qkLGu2yeJw_TKBx
    php artisan env:set NOCAPTCHA_SITEKEY=6LclVH8UAAAAAD65E-FQC9fUdoijUQ--fdQk6k2I
@endtask

@task('copy', ['on' => 'web'])
    cp -r {{ $current }}/storage/app/public/FTUploads/ {{ $release }}/storage/app/public/
@endtask
