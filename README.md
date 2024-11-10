# Controller CRUD Helpers
## Overview
This package is a set of helper traits for Laravel controllers. It provides a convenient and reusable way to handle common tasks such as API datatable, data storage, and data update and other crud operations in Laravel applications.

![Tests Workflow](https://github.com/miladshm/controller-helpers/actions/workflows/tests.yml/badge.svg)
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

### Store method with HasStore trait

The HasStore trait is part of the miladshm/controller-helpers package and is designed to handle the creation of new
model instances in Laravel controllers. It provides a convenient and reusable way to perform common tasks related to
storing data in your application.

#### Usage

To use the HasStore trait in your Laravel controller, follow these steps:

1. Include the trait in your controller class:

````php
use Miladshm\ControllerHelpers\Http\Traits\HasStore;

class YourController extends Controller
{
    use HasStore;

    // Your other methods and properties
}
````

2. Implement the necessary methods and properties:

Define the model class used by the trait:

````php
use App\Models\YourModel;

class YourController extends Controller
{
use HasStore;

    protected function model(): string
    {
        return new YourModel();
    }

    // Your other methods and properties
}
````

Override the storeCallback method if you need to perform additional actions after a new model instance is created:

````php
use App\Models\YourModel;

class YourController extends Controller
{
use HasStore;

    protected function model(): string
    {
        return new YourModel();
    }

    protected function storeCallback(Request $request, Model $item): void
    {
        // Perform additional actions here
    }

    // Your other methods and properties
}
````

Override the prepareForStore method if you need to perform any necessary preparations before a new model instance is
created:

````php
use App\Models\YourModel;

class YourController extends Controller
{
use HasStore;

    protected function model(): string
    {
        return new YourModel();
    }

    protected function prepareForStore(Request &$request)
    {
        // Perform any necessary preparations here
    }

    // Your other methods and properties
}
````

3. Implement the validation logic:

Implement the `requestClass` method to define the form request class for the incoming request data:

````php
use App\Models\YourModel;

class YourController extends Controller
{
use HasStore;

    protected function model(): string
    {
        return new YourModel();
    }

    /**
     * @return FormRequest
     */
    private function requestClass(): FormRequest
    {
        return new StoreRequest;
    }

    // Your other methods and properties
}
````

4. Customizing the response data:

If you want to customize the response when the new model instance is created, you can implement the
`getJsonResourceClass` method and return your desired `Api Resource Class`:

````php
use App\Models\YourModel;
use App\Http\Resources\YourModelResource;

class YourController extends Controller
{
    use HasStore;

    protected function model(): string
    {
        return new YourModel();
    }
    
        /**
     * @return JsonResource|class-string|null
     */
    public function getJsonResourceClass(): JsonResource|string|null
    {
        return YourModelResource::class;
    }
    // Your other methods and properties
}
````

That's it! You now have a working implementation of the HasStore trait in your Laravel controller. You can customize the
trait further to fit your specific needs.

### Update data with HasUpdate trait

The HasUpdate trait is part of the `miladshm/controller-helpers` package and is designed to handle the updating of
specific items in your Laravel application. It provides a convenient and reusable way to perform common tasks related to
updating data in your controllers.

#### Usage

To use the `HasUpdate` trait in your Laravel controller, follow these steps:

1. Include the trait in your controller class:

````php
use Miladshm\ControllerHelpers\Http\Traits\HasUpdate;

class YourController extends Controller
{
    use HasUpdate;

    // Your other methods and properties
}
````

2. Implement the necessary methods and properties:

Define the model class used by the trait:

````php
use App\Models\YourModel;

class YourController extends Controller
{
    use HasUpdate;

    protected function model(): string
    {
        return new YourModel();
    }

    // Your other methods and properties
}
````

Override the prepareForUpdate method if you need to perform any necessary preparations before updating a model instance:

````php
use App\Models\YourModel;

class YourController extends Controller
{
    use HasUpdate;

    protected function model(): string
    {
        return new YourModel();
    }

    protected function prepareForUpdate(Request &$request): void
    {
        // Perform any necessary preparations here
        // Modify the request object as necessary
    }

    // Your other methods and properties
}
````

Override the updateCallback method if you need to perform additional actions after a model instance is updated:

````php
use App\Models\YourModel;

class YourController extends Controller
{
    use HasUpdate;

    protected function model(): string
    {
        return new YourModel();
    }

    protected function updateCallback(Request $request, Model $item): void
    {
        // Perform any additional actions here
    }

    // Your other methods and properties
}
````

Validation logic can be implemented by overriding the `updateRequestClass` method (Optional):
if this method not implemented trait will use the class that provided by `requestClass`

````php
use App\Models\YourModel;
use App\Http\Requests\YourModelUpdateRequest;

class YourController extends Controller
{
    use HasUpdate;

    protected function model(): string
    {
        return new YourModel();
    }

    protected function updateRequestClass(): ?FormRequest
    {
        return new YourModelUpdateRequest();
    }

    // Your other methods and properties
}
````

That's it! You now have a working implementation of the HasUpdate trait in your Laravel controller. You can customize
the trait further to fit your specific needs.

### Change Position

#### Introduction:

The HasChangePosition trait is designed to provide a method for changing the position of an item in a sorted list. It
uses the provided ChangePositionRequest to determine the direction of movement. This document will guide you through the
usage of this trait in your Laravel application.

#### Usage:

1. Include the trait in your controller class:

````php
use Miladshm\ControllerHelpers\Http\Traits\HasChangePosition;
class YourController extends Controller
{
use HasChangePosition;

    // Your other methods and properties
}
````

2. Define the model used in the trait:

````php
use App\Models\YourModel;

class YourController extends Controller
{
    use HasChangePosition;

    protected function model()
    {
        return new YourModel();
    }

    // Your other methods and properties
}
````

3. Implement the filters method (optional):
   If you need to apply any filters to the query when retrieving the adjacent item, you can override the filters method.
   This method should accept a query builder instance and return the modified query builder.

````php
use App\Models\YourModel;

class YourController extends Controller
{
    use HasChangePosition;

    protected function model()
    {
        return YourModel::class;
    }

    protected function filters($query)
    {
        return $query->where('status', 'active');
    }

    // Your other methods and properties
}
````

4. Define the getPositionColumn method (optional):
   If the name of the column used for sorting is different from the default (order_column), you can override the
   getPositionColumn method to return the correct column name.

````php
use App\Models\YourModel;

class YourController extends Controller
{
    use HasChangePosition;

    protected function model()
    {
        return YourModel::class;
    }

    protected function getPositionColumn()
    {
        return 'your_position_column';
    }

    // Your other methods and properties
}
````

Remember to handle any exceptions that may be thrown by the changePosition method, such as ValidationException if the
item cannot be moved in the specified direction.

That's it! You now have a working implementation of the HasChangePosition trait in your Laravel application.


