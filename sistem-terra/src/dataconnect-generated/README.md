# Generated TypeScript README
This README will guide you through the process of using the generated JavaScript SDK package for the connector `example`. It will also provide examples on how to use your generated SDK to call your Data Connect queries and mutations.

***NOTE:** This README is generated alongside the generated SDK. If you make changes to this file, they will be overwritten when the SDK is regenerated.*

# Table of Contents
- [**Overview**](#generated-javascript-readme)
- [**Accessing the connector**](#accessing-the-connector)
  - [*Connecting to the local Emulator*](#connecting-to-the-local-emulator)
- [**Queries**](#queries)
  - [*ListVideoSources*](#listvideosources)
  - [*ListDetectionRulesForVideoSource*](#listdetectionrulesforvideosource)
- [**Mutations**](#mutations)
  - [*CreateDetectionEvent*](#createdetectionevent)
  - [*UpdateVideoSourceStatus*](#updatevideosourcestatus)

# Accessing the connector
A connector is a collection of Queries and Mutations. One SDK is generated for each connector - this SDK is generated for the connector `example`. You can find more information about connectors in the [Data Connect documentation](https://firebase.google.com/docs/data-connect#how-does).

You can use this generated SDK by importing from the package `@dataconnect/generated` as shown below. Both CommonJS and ESM imports are supported.

You can also follow the instructions from the [Data Connect documentation](https://firebase.google.com/docs/data-connect/web-sdk#set-client).

```typescript
import { getDataConnect } from 'firebase/data-connect';
import { connectorConfig } from '@dataconnect/generated';

const dataConnect = getDataConnect(connectorConfig);
```

## Connecting to the local Emulator
By default, the connector will connect to the production service.

To connect to the emulator, you can use the following code.
You can also follow the emulator instructions from the [Data Connect documentation](https://firebase.google.com/docs/data-connect/web-sdk#instrument-clients).

```typescript
import { connectDataConnectEmulator, getDataConnect } from 'firebase/data-connect';
import { connectorConfig } from '@dataconnect/generated';

const dataConnect = getDataConnect(connectorConfig);
connectDataConnectEmulator(dataConnect, 'localhost', 9399);
```

After it's initialized, you can call your Data Connect [queries](#queries) and [mutations](#mutations) from your generated SDK.

# Queries

There are two ways to execute a Data Connect Query using the generated Web SDK:
- Using a Query Reference function, which returns a `QueryRef`
  - The `QueryRef` can be used as an argument to `executeQuery()`, which will execute the Query and return a `QueryPromise`
- Using an action shortcut function, which returns a `QueryPromise`
  - Calling the action shortcut function will execute the Query and return a `QueryPromise`

The following is true for both the action shortcut function and the `QueryRef` function:
- The `QueryPromise` returned will resolve to the result of the Query once it has finished executing
- If the Query accepts arguments, both the action shortcut function and the `QueryRef` function accept a single argument: an object that contains all the required variables (and the optional variables) for the Query
- Both functions can be called with or without passing in a `DataConnect` instance as an argument. If no `DataConnect` argument is passed in, then the generated SDK will call `getDataConnect(connectorConfig)` behind the scenes for you.

Below are examples of how to use the `example` connector's generated functions to execute each query. You can also follow the examples from the [Data Connect documentation](https://firebase.google.com/docs/data-connect/web-sdk#using-queries).

## ListVideoSources
You can execute the `ListVideoSources` query using the following action shortcut function, or by calling `executeQuery()` after calling the following `QueryRef` function, both of which are defined in [dataconnect-generated/index.d.ts](./index.d.ts):
```typescript
listVideoSources(): QueryPromise<ListVideoSourcesData, undefined>;

interface ListVideoSourcesRef {
  ...
  /* Allow users to create refs without passing in DataConnect */
  (): QueryRef<ListVideoSourcesData, undefined>;
}
export const listVideoSourcesRef: ListVideoSourcesRef;
```
You can also pass in a `DataConnect` instance to the action shortcut function or `QueryRef` function.
```typescript
listVideoSources(dc: DataConnect): QueryPromise<ListVideoSourcesData, undefined>;

interface ListVideoSourcesRef {
  ...
  (dc: DataConnect): QueryRef<ListVideoSourcesData, undefined>;
}
export const listVideoSourcesRef: ListVideoSourcesRef;
```

If you need the name of the operation without creating a ref, you can retrieve the operation name by calling the `operationName` property on the listVideoSourcesRef:
```typescript
const name = listVideoSourcesRef.operationName;
console.log(name);
```

### Variables
The `ListVideoSources` query has no variables.
### Return Type
Recall that executing the `ListVideoSources` query returns a `QueryPromise` that resolves to an object with a `data` property.

The `data` property is an object of type `ListVideoSourcesData`, which is defined in [dataconnect-generated/index.d.ts](./index.d.ts). It has the following fields:
```typescript
export interface ListVideoSourcesData {
  videoSources: ({
    id: UUIDString;
    name: string;
    sourceUrl: string;
    status: string;
  } & VideoSource_Key)[];
}
```
### Using `ListVideoSources`'s action shortcut function

```typescript
import { getDataConnect } from 'firebase/data-connect';
import { connectorConfig, listVideoSources } from '@dataconnect/generated';


// Call the `listVideoSources()` function to execute the query.
// You can use the `await` keyword to wait for the promise to resolve.
const { data } = await listVideoSources();

// You can also pass in a `DataConnect` instance to the action shortcut function.
const dataConnect = getDataConnect(connectorConfig);
const { data } = await listVideoSources(dataConnect);

console.log(data.videoSources);

// Or, you can use the `Promise` API.
listVideoSources().then((response) => {
  const data = response.data;
  console.log(data.videoSources);
});
```

### Using `ListVideoSources`'s `QueryRef` function

```typescript
import { getDataConnect, executeQuery } from 'firebase/data-connect';
import { connectorConfig, listVideoSourcesRef } from '@dataconnect/generated';


// Call the `listVideoSourcesRef()` function to get a reference to the query.
const ref = listVideoSourcesRef();

// You can also pass in a `DataConnect` instance to the `QueryRef` function.
const dataConnect = getDataConnect(connectorConfig);
const ref = listVideoSourcesRef(dataConnect);

// Call `executeQuery()` on the reference to execute the query.
// You can use the `await` keyword to wait for the promise to resolve.
const { data } = await executeQuery(ref);

console.log(data.videoSources);

// Or, you can use the `Promise` API.
executeQuery(ref).then((response) => {
  const data = response.data;
  console.log(data.videoSources);
});
```

## ListDetectionRulesForVideoSource
You can execute the `ListDetectionRulesForVideoSource` query using the following action shortcut function, or by calling `executeQuery()` after calling the following `QueryRef` function, both of which are defined in [dataconnect-generated/index.d.ts](./index.d.ts):
```typescript
listDetectionRulesForVideoSource(vars: ListDetectionRulesForVideoSourceVariables): QueryPromise<ListDetectionRulesForVideoSourceData, ListDetectionRulesForVideoSourceVariables>;

interface ListDetectionRulesForVideoSourceRef {
  ...
  /* Allow users to create refs without passing in DataConnect */
  (vars: ListDetectionRulesForVideoSourceVariables): QueryRef<ListDetectionRulesForVideoSourceData, ListDetectionRulesForVideoSourceVariables>;
}
export const listDetectionRulesForVideoSourceRef: ListDetectionRulesForVideoSourceRef;
```
You can also pass in a `DataConnect` instance to the action shortcut function or `QueryRef` function.
```typescript
listDetectionRulesForVideoSource(dc: DataConnect, vars: ListDetectionRulesForVideoSourceVariables): QueryPromise<ListDetectionRulesForVideoSourceData, ListDetectionRulesForVideoSourceVariables>;

interface ListDetectionRulesForVideoSourceRef {
  ...
  (dc: DataConnect, vars: ListDetectionRulesForVideoSourceVariables): QueryRef<ListDetectionRulesForVideoSourceData, ListDetectionRulesForVideoSourceVariables>;
}
export const listDetectionRulesForVideoSourceRef: ListDetectionRulesForVideoSourceRef;
```

If you need the name of the operation without creating a ref, you can retrieve the operation name by calling the `operationName` property on the listDetectionRulesForVideoSourceRef:
```typescript
const name = listDetectionRulesForVideoSourceRef.operationName;
console.log(name);
```

### Variables
The `ListDetectionRulesForVideoSource` query requires an argument of type `ListDetectionRulesForVideoSourceVariables`, which is defined in [dataconnect-generated/index.d.ts](./index.d.ts). It has the following fields:

```typescript
export interface ListDetectionRulesForVideoSourceVariables {
  videoSourceId: UUIDString;
}
```
### Return Type
Recall that executing the `ListDetectionRulesForVideoSource` query returns a `QueryPromise` that resolves to an object with a `data` property.

The `data` property is an object of type `ListDetectionRulesForVideoSourceData`, which is defined in [dataconnect-generated/index.d.ts](./index.d.ts). It has the following fields:
```typescript
export interface ListDetectionRulesForVideoSourceData {
  detectionRules: ({
    id: UUIDString;
    name: string;
    detectionTarget: string;
    confidenceThreshold?: number | null;
    isActive: boolean;
  } & DetectionRule_Key)[];
}
```
### Using `ListDetectionRulesForVideoSource`'s action shortcut function

```typescript
import { getDataConnect } from 'firebase/data-connect';
import { connectorConfig, listDetectionRulesForVideoSource, ListDetectionRulesForVideoSourceVariables } from '@dataconnect/generated';

// The `ListDetectionRulesForVideoSource` query requires an argument of type `ListDetectionRulesForVideoSourceVariables`:
const listDetectionRulesForVideoSourceVars: ListDetectionRulesForVideoSourceVariables = {
  videoSourceId: ..., 
};

// Call the `listDetectionRulesForVideoSource()` function to execute the query.
// You can use the `await` keyword to wait for the promise to resolve.
const { data } = await listDetectionRulesForVideoSource(listDetectionRulesForVideoSourceVars);
// Variables can be defined inline as well.
const { data } = await listDetectionRulesForVideoSource({ videoSourceId: ..., });

// You can also pass in a `DataConnect` instance to the action shortcut function.
const dataConnect = getDataConnect(connectorConfig);
const { data } = await listDetectionRulesForVideoSource(dataConnect, listDetectionRulesForVideoSourceVars);

console.log(data.detectionRules);

// Or, you can use the `Promise` API.
listDetectionRulesForVideoSource(listDetectionRulesForVideoSourceVars).then((response) => {
  const data = response.data;
  console.log(data.detectionRules);
});
```

### Using `ListDetectionRulesForVideoSource`'s `QueryRef` function

```typescript
import { getDataConnect, executeQuery } from 'firebase/data-connect';
import { connectorConfig, listDetectionRulesForVideoSourceRef, ListDetectionRulesForVideoSourceVariables } from '@dataconnect/generated';

// The `ListDetectionRulesForVideoSource` query requires an argument of type `ListDetectionRulesForVideoSourceVariables`:
const listDetectionRulesForVideoSourceVars: ListDetectionRulesForVideoSourceVariables = {
  videoSourceId: ..., 
};

// Call the `listDetectionRulesForVideoSourceRef()` function to get a reference to the query.
const ref = listDetectionRulesForVideoSourceRef(listDetectionRulesForVideoSourceVars);
// Variables can be defined inline as well.
const ref = listDetectionRulesForVideoSourceRef({ videoSourceId: ..., });

// You can also pass in a `DataConnect` instance to the `QueryRef` function.
const dataConnect = getDataConnect(connectorConfig);
const ref = listDetectionRulesForVideoSourceRef(dataConnect, listDetectionRulesForVideoSourceVars);

// Call `executeQuery()` on the reference to execute the query.
// You can use the `await` keyword to wait for the promise to resolve.
const { data } = await executeQuery(ref);

console.log(data.detectionRules);

// Or, you can use the `Promise` API.
executeQuery(ref).then((response) => {
  const data = response.data;
  console.log(data.detectionRules);
});
```

# Mutations

There are two ways to execute a Data Connect Mutation using the generated Web SDK:
- Using a Mutation Reference function, which returns a `MutationRef`
  - The `MutationRef` can be used as an argument to `executeMutation()`, which will execute the Mutation and return a `MutationPromise`
- Using an action shortcut function, which returns a `MutationPromise`
  - Calling the action shortcut function will execute the Mutation and return a `MutationPromise`

The following is true for both the action shortcut function and the `MutationRef` function:
- The `MutationPromise` returned will resolve to the result of the Mutation once it has finished executing
- If the Mutation accepts arguments, both the action shortcut function and the `MutationRef` function accept a single argument: an object that contains all the required variables (and the optional variables) for the Mutation
- Both functions can be called with or without passing in a `DataConnect` instance as an argument. If no `DataConnect` argument is passed in, then the generated SDK will call `getDataConnect(connectorConfig)` behind the scenes for you.

Below are examples of how to use the `example` connector's generated functions to execute each mutation. You can also follow the examples from the [Data Connect documentation](https://firebase.google.com/docs/data-connect/web-sdk#using-mutations).

## CreateDetectionEvent
You can execute the `CreateDetectionEvent` mutation using the following action shortcut function, or by calling `executeMutation()` after calling the following `MutationRef` function, both of which are defined in [dataconnect-generated/index.d.ts](./index.d.ts):
```typescript
createDetectionEvent(vars: CreateDetectionEventVariables): MutationPromise<CreateDetectionEventData, CreateDetectionEventVariables>;

interface CreateDetectionEventRef {
  ...
  /* Allow users to create refs without passing in DataConnect */
  (vars: CreateDetectionEventVariables): MutationRef<CreateDetectionEventData, CreateDetectionEventVariables>;
}
export const createDetectionEventRef: CreateDetectionEventRef;
```
You can also pass in a `DataConnect` instance to the action shortcut function or `MutationRef` function.
```typescript
createDetectionEvent(dc: DataConnect, vars: CreateDetectionEventVariables): MutationPromise<CreateDetectionEventData, CreateDetectionEventVariables>;

interface CreateDetectionEventRef {
  ...
  (dc: DataConnect, vars: CreateDetectionEventVariables): MutationRef<CreateDetectionEventData, CreateDetectionEventVariables>;
}
export const createDetectionEventRef: CreateDetectionEventRef;
```

If you need the name of the operation without creating a ref, you can retrieve the operation name by calling the `operationName` property on the createDetectionEventRef:
```typescript
const name = createDetectionEventRef.operationName;
console.log(name);
```

### Variables
The `CreateDetectionEvent` mutation requires an argument of type `CreateDetectionEventVariables`, which is defined in [dataconnect-generated/index.d.ts](./index.d.ts). It has the following fields:

```typescript
export interface CreateDetectionEventVariables {
  detectionRuleId: UUIDString;
  videoSourceId: UUIDString;
  boundingBox: string;
  confidence: number;
  detectedObject: string;
  timestamp: TimestampString;
}
```
### Return Type
Recall that executing the `CreateDetectionEvent` mutation returns a `MutationPromise` that resolves to an object with a `data` property.

The `data` property is an object of type `CreateDetectionEventData`, which is defined in [dataconnect-generated/index.d.ts](./index.d.ts). It has the following fields:
```typescript
export interface CreateDetectionEventData {
  detectionEvent_insert: DetectionEvent_Key;
}
```
### Using `CreateDetectionEvent`'s action shortcut function

```typescript
import { getDataConnect } from 'firebase/data-connect';
import { connectorConfig, createDetectionEvent, CreateDetectionEventVariables } from '@dataconnect/generated';

// The `CreateDetectionEvent` mutation requires an argument of type `CreateDetectionEventVariables`:
const createDetectionEventVars: CreateDetectionEventVariables = {
  detectionRuleId: ..., 
  videoSourceId: ..., 
  boundingBox: ..., 
  confidence: ..., 
  detectedObject: ..., 
  timestamp: ..., 
};

// Call the `createDetectionEvent()` function to execute the mutation.
// You can use the `await` keyword to wait for the promise to resolve.
const { data } = await createDetectionEvent(createDetectionEventVars);
// Variables can be defined inline as well.
const { data } = await createDetectionEvent({ detectionRuleId: ..., videoSourceId: ..., boundingBox: ..., confidence: ..., detectedObject: ..., timestamp: ..., });

// You can also pass in a `DataConnect` instance to the action shortcut function.
const dataConnect = getDataConnect(connectorConfig);
const { data } = await createDetectionEvent(dataConnect, createDetectionEventVars);

console.log(data.detectionEvent_insert);

// Or, you can use the `Promise` API.
createDetectionEvent(createDetectionEventVars).then((response) => {
  const data = response.data;
  console.log(data.detectionEvent_insert);
});
```

### Using `CreateDetectionEvent`'s `MutationRef` function

```typescript
import { getDataConnect, executeMutation } from 'firebase/data-connect';
import { connectorConfig, createDetectionEventRef, CreateDetectionEventVariables } from '@dataconnect/generated';

// The `CreateDetectionEvent` mutation requires an argument of type `CreateDetectionEventVariables`:
const createDetectionEventVars: CreateDetectionEventVariables = {
  detectionRuleId: ..., 
  videoSourceId: ..., 
  boundingBox: ..., 
  confidence: ..., 
  detectedObject: ..., 
  timestamp: ..., 
};

// Call the `createDetectionEventRef()` function to get a reference to the mutation.
const ref = createDetectionEventRef(createDetectionEventVars);
// Variables can be defined inline as well.
const ref = createDetectionEventRef({ detectionRuleId: ..., videoSourceId: ..., boundingBox: ..., confidence: ..., detectedObject: ..., timestamp: ..., });

// You can also pass in a `DataConnect` instance to the `MutationRef` function.
const dataConnect = getDataConnect(connectorConfig);
const ref = createDetectionEventRef(dataConnect, createDetectionEventVars);

// Call `executeMutation()` on the reference to execute the mutation.
// You can use the `await` keyword to wait for the promise to resolve.
const { data } = await executeMutation(ref);

console.log(data.detectionEvent_insert);

// Or, you can use the `Promise` API.
executeMutation(ref).then((response) => {
  const data = response.data;
  console.log(data.detectionEvent_insert);
});
```

## UpdateVideoSourceStatus
You can execute the `UpdateVideoSourceStatus` mutation using the following action shortcut function, or by calling `executeMutation()` after calling the following `MutationRef` function, both of which are defined in [dataconnect-generated/index.d.ts](./index.d.ts):
```typescript
updateVideoSourceStatus(vars: UpdateVideoSourceStatusVariables): MutationPromise<UpdateVideoSourceStatusData, UpdateVideoSourceStatusVariables>;

interface UpdateVideoSourceStatusRef {
  ...
  /* Allow users to create refs without passing in DataConnect */
  (vars: UpdateVideoSourceStatusVariables): MutationRef<UpdateVideoSourceStatusData, UpdateVideoSourceStatusVariables>;
}
export const updateVideoSourceStatusRef: UpdateVideoSourceStatusRef;
```
You can also pass in a `DataConnect` instance to the action shortcut function or `MutationRef` function.
```typescript
updateVideoSourceStatus(dc: DataConnect, vars: UpdateVideoSourceStatusVariables): MutationPromise<UpdateVideoSourceStatusData, UpdateVideoSourceStatusVariables>;

interface UpdateVideoSourceStatusRef {
  ...
  (dc: DataConnect, vars: UpdateVideoSourceStatusVariables): MutationRef<UpdateVideoSourceStatusData, UpdateVideoSourceStatusVariables>;
}
export const updateVideoSourceStatusRef: UpdateVideoSourceStatusRef;
```

If you need the name of the operation without creating a ref, you can retrieve the operation name by calling the `operationName` property on the updateVideoSourceStatusRef:
```typescript
const name = updateVideoSourceStatusRef.operationName;
console.log(name);
```

### Variables
The `UpdateVideoSourceStatus` mutation requires an argument of type `UpdateVideoSourceStatusVariables`, which is defined in [dataconnect-generated/index.d.ts](./index.d.ts). It has the following fields:

```typescript
export interface UpdateVideoSourceStatusVariables {
  id: UUIDString;
  status: string;
}
```
### Return Type
Recall that executing the `UpdateVideoSourceStatus` mutation returns a `MutationPromise` that resolves to an object with a `data` property.

The `data` property is an object of type `UpdateVideoSourceStatusData`, which is defined in [dataconnect-generated/index.d.ts](./index.d.ts). It has the following fields:
```typescript
export interface UpdateVideoSourceStatusData {
  videoSource_update?: VideoSource_Key | null;
}
```
### Using `UpdateVideoSourceStatus`'s action shortcut function

```typescript
import { getDataConnect } from 'firebase/data-connect';
import { connectorConfig, updateVideoSourceStatus, UpdateVideoSourceStatusVariables } from '@dataconnect/generated';

// The `UpdateVideoSourceStatus` mutation requires an argument of type `UpdateVideoSourceStatusVariables`:
const updateVideoSourceStatusVars: UpdateVideoSourceStatusVariables = {
  id: ..., 
  status: ..., 
};

// Call the `updateVideoSourceStatus()` function to execute the mutation.
// You can use the `await` keyword to wait for the promise to resolve.
const { data } = await updateVideoSourceStatus(updateVideoSourceStatusVars);
// Variables can be defined inline as well.
const { data } = await updateVideoSourceStatus({ id: ..., status: ..., });

// You can also pass in a `DataConnect` instance to the action shortcut function.
const dataConnect = getDataConnect(connectorConfig);
const { data } = await updateVideoSourceStatus(dataConnect, updateVideoSourceStatusVars);

console.log(data.videoSource_update);

// Or, you can use the `Promise` API.
updateVideoSourceStatus(updateVideoSourceStatusVars).then((response) => {
  const data = response.data;
  console.log(data.videoSource_update);
});
```

### Using `UpdateVideoSourceStatus`'s `MutationRef` function

```typescript
import { getDataConnect, executeMutation } from 'firebase/data-connect';
import { connectorConfig, updateVideoSourceStatusRef, UpdateVideoSourceStatusVariables } from '@dataconnect/generated';

// The `UpdateVideoSourceStatus` mutation requires an argument of type `UpdateVideoSourceStatusVariables`:
const updateVideoSourceStatusVars: UpdateVideoSourceStatusVariables = {
  id: ..., 
  status: ..., 
};

// Call the `updateVideoSourceStatusRef()` function to get a reference to the mutation.
const ref = updateVideoSourceStatusRef(updateVideoSourceStatusVars);
// Variables can be defined inline as well.
const ref = updateVideoSourceStatusRef({ id: ..., status: ..., });

// You can also pass in a `DataConnect` instance to the `MutationRef` function.
const dataConnect = getDataConnect(connectorConfig);
const ref = updateVideoSourceStatusRef(dataConnect, updateVideoSourceStatusVars);

// Call `executeMutation()` on the reference to execute the mutation.
// You can use the `await` keyword to wait for the promise to resolve.
const { data } = await executeMutation(ref);

console.log(data.videoSource_update);

// Or, you can use the `Promise` API.
executeMutation(ref).then((response) => {
  const data = response.data;
  console.log(data.videoSource_update);
});
```

