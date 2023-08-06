# Controller CRUD Helpers

## Install

```shell
  composer require miladshm/controller-helpers
```

### Configuration

```shell
  php artisan vendor:publish --tag controller-helpers-config
```

## Usage

### API Datatable

````php
use Miladshm\ControllerHelpers\Http\Traits\HasApiDatatable;

class TestController extends Controller
{
    use HasApiDatatable;
    
     /**
     * Specifying the model class to use
     * 
     * @return Model
     */
    private function model(): Model
    {
        return new TestModel;
    }
    
    /**
     * Specifying extra data to send with index response
     * 
     * @param Model|null $item
     * @return array|null
     */
    private function extraData(Model $item = null): ?array
    {
        return [];
    }

    /**
     * Specifying Model relations to load with data
     * @return array
     */
    private function relations(): array
    {
        return [];
    }
}
````

#### Set filters

````php

use Miladshm\ControllerHelpers\Http\Traits\HasApiDatatable;

class TestController extends Controller
{
    use HasApiDatatable;
    
    ...
    
    /**
    * @param Builder $builder
    * @return Builder|null
    */
    protected function filters(Builder $builder): null|Builder
    {
        return $builder
                    ->when('some_condition',
                        fn(Builder $builder) => $builder->where('field', 'value'))
                    ->when(\request()->filled('status'), function (Builder $builder) {
                        $builder->where('status', \request()->input('status'));
                    })
                    ->where('field2', 'value2'));
    }
    
    ...
}
````

#### Set default order

````php

use Miladshm\ControllerHelpers\Http\Traits\HasApiDatatable;

class TestController extends Controller
{
    use HasApiDatatable;
    
    /**
     * Order can be either 'asc' or 'desc', default value in 'desc'
     * @return string
     */
    protected function getOrder(): string
    {
        return 'desc'; // value can be 'desc' or 'asc'
    } 
    
    ...
}
````

#### Set default page length

````php

use Miladshm\ControllerHelpers\Http\Traits\HasApiDatatable;

class TestController extends Controller
{
    use HasApiDatatable;
    
    /**
     * @return int
     */
    protected function getPageLength(): int
    {
        return 15;
    }
    
    ...
}
````

#### Set table columns to be in response

````php

use Miladshm\ControllerHelpers\Http\Traits\HasApiDatatable;

class TestController extends Controller
{
    use HasApiDatatable;
    
    /**
     * @return array|string[]
     */
    public function getColumns(): array
    {
        return ['id', 'code', 'name'];
    }
    
    ...
}
````

#### Set columns and first-hand relations to searching within

````php

use Miladshm\ControllerHelpers\Http\Traits\HasApiDatatable;

class TestController extends Controller
{
    use HasApiDatatable;
    
    /**
     * @return array
     */
    protected function getSearchable(): array
    {
        return ['id', 'code', 'name', 'relation.column']; // You can specify relation columns to search within
    }
    ...
}
````

#### Set pagination type

You can choose index pagination type between default, simple and cursor paginator.

````php

use Miladshm\ControllerHelpers\Http\Traits\HasApiDatatable;

class TestController extends Controller
{
    use HasApiDatatable;
    
    protected function getPaginator(): ?string
    {
        return 'simple'; // value can be [default,simple,cursor]
    }
    
    ...
}
````