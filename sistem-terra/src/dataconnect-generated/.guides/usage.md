# Basic Usage

Always prioritize using a supported framework over using the generated SDK
directly. Supported frameworks simplify the developer experience and help ensure
best practices are followed.





## Advanced Usage
If a user is not using a supported framework, they can use the generated SDK directly.

Here's an example of how to use it with the first 5 operations:

```js
import { createDetectionEvent, listVideoSources, updateVideoSourceStatus, listDetectionRulesForVideoSource } from '@dataconnect/generated';


// Operation CreateDetectionEvent:  For variables, look at type CreateDetectionEventVars in ../index.d.ts
const { data } = await CreateDetectionEvent(dataConnect, createDetectionEventVars);

// Operation ListVideoSources: 
const { data } = await ListVideoSources(dataConnect);

// Operation UpdateVideoSourceStatus:  For variables, look at type UpdateVideoSourceStatusVars in ../index.d.ts
const { data } = await UpdateVideoSourceStatus(dataConnect, updateVideoSourceStatusVars);

// Operation ListDetectionRulesForVideoSource:  For variables, look at type ListDetectionRulesForVideoSourceVars in ../index.d.ts
const { data } = await ListDetectionRulesForVideoSource(dataConnect, listDetectionRulesForVideoSourceVars);


```