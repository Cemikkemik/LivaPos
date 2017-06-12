<div ng-repeat="field in fields">
    <div class="form-group" ng-if="field.type == 'text'">
        <label for="">{{ field.name }}</label>
        <input ng-required="field.required" name="{{ field.model }}" type="text" ng-model="item[ field.model ]" class="form-control" id="" placeholder="{{ field.placeholder }}">
        <p class="help-block">{{ field.description }}</p>
        <p ng-show="form.$submitted || form.{{ field.model }}.$touched" class="help-block">
            <span ng-show="form.{{ field.model }}.$error.required"><?php echo _s( 'Ce champ est requis', 'nexo' );?></span>
        </p>
    </div>
    <div class="form-group" ng-if="field.type == 'date'">
        <label for="">{{ field.name }}</label>
        <input ng-required="field.required" name="{{ field.model }}" type="date" ng-model="item[ field.model ]" class="form-control" id="" placeholder="{{ field.placeholder }}">
        <p class="help-block">{{ field.description }}</p>
        <p class="help-block" ng-show="form.{{ field.model }}.$error.required"><?php echo _s( 'Ce champ est requis', 'nexo' );?></p>
    </div>
    <div class="form-group" ng-if="field.type == 'upload'">
        <label for="">{{ field.name }}</label>
        <input ng-required="field.required" name="{{ field.model }}" type="file" ng-model="item[ field.model ]" class="form-control" id="" placeholder="{{ field.placeholder }}">
        <p class="help-block">{{ field.description }}</p>
        <p class="help-block" ng-show="form.{{ field.model }}.$error.required"><?php echo _s( 'Ce champ est requis', 'nexo' );?></p>
    </div>
    <div class="form-group" ng-if="field.type == 'select'">
        <label for="">{{ field.name }}</label>
        <select ng-required="field.required" ng-options="option.key as option.value for option in field.options" name="{{ field.model }}" type="file" ng-model="item[ field.model ]" class="form-control" id="" placeholder="{{ field.placeholder }}"></select>
        <p class="help-block">{{ field.description }}</p>
        <p class="help-block" ng-show="form.{{ field.model }}.$error.required"><?php echo _s( 'Ce champ est requis', 'nexo' );?></p>
    </div>
    <div class="form-group" ng-if="field.type == 'email'">
        <label for="">{{ field.name }}</label>
        <input ng-required="field.required" name="{{ field.model }}" type="email" ng-model="item[ field.model ]" class="form-control" id="" placeholder="{{ field.placeholder }}">
        <p class="help-block">{{ field.description }}</p>
        <p class="help-block" ng-show="form.{{ field.model }}.$error.required"><?php echo _s( 'Ce champ est requis', 'nexo' );?></p>
        <p class="help-block" ng-show="form.{{ field.model }}.$error.email"><?php echo _s( 'Ce champ doit contenir une addresse email valide.', 'nexo' );?></p>
    </div>
</div>