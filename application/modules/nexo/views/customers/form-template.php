<div ng-if="tab.namespace == 'basic' || tab.namespace == 'billing' || tab.namespace == 'shipping'">
    <div sf-schema="schema[ tab.namespace ]" sf-form="form[ tab.namespace ]" sf-model="model[ tab.namespace ]"></div>
</div>
