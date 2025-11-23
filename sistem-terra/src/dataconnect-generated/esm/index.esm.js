import { queryRef, executeQuery, mutationRef, executeMutation, validateArgs } from 'firebase/data-connect';

export const connectorConfig = {
  connector: 'example',
  service: 'terra-sm5pro',
  location: 'us-east4'
};

export const createDetectionEventRef = (dcOrVars, vars) => {
  const { dc: dcInstance, vars: inputVars} = validateArgs(connectorConfig, dcOrVars, vars, true);
  dcInstance._useGeneratedSdk();
  return mutationRef(dcInstance, 'CreateDetectionEvent', inputVars);
}
createDetectionEventRef.operationName = 'CreateDetectionEvent';

export function createDetectionEvent(dcOrVars, vars) {
  return executeMutation(createDetectionEventRef(dcOrVars, vars));
}

export const listVideoSourcesRef = (dc) => {
  const { dc: dcInstance} = validateArgs(connectorConfig, dc, undefined);
  dcInstance._useGeneratedSdk();
  return queryRef(dcInstance, 'ListVideoSources');
}
listVideoSourcesRef.operationName = 'ListVideoSources';

export function listVideoSources(dc) {
  return executeQuery(listVideoSourcesRef(dc));
}

export const updateVideoSourceStatusRef = (dcOrVars, vars) => {
  const { dc: dcInstance, vars: inputVars} = validateArgs(connectorConfig, dcOrVars, vars, true);
  dcInstance._useGeneratedSdk();
  return mutationRef(dcInstance, 'UpdateVideoSourceStatus', inputVars);
}
updateVideoSourceStatusRef.operationName = 'UpdateVideoSourceStatus';

export function updateVideoSourceStatus(dcOrVars, vars) {
  return executeMutation(updateVideoSourceStatusRef(dcOrVars, vars));
}

export const listDetectionRulesForVideoSourceRef = (dcOrVars, vars) => {
  const { dc: dcInstance, vars: inputVars} = validateArgs(connectorConfig, dcOrVars, vars, true);
  dcInstance._useGeneratedSdk();
  return queryRef(dcInstance, 'ListDetectionRulesForVideoSource', inputVars);
}
listDetectionRulesForVideoSourceRef.operationName = 'ListDetectionRulesForVideoSource';

export function listDetectionRulesForVideoSource(dcOrVars, vars) {
  return executeQuery(listDetectionRulesForVideoSourceRef(dcOrVars, vars));
}

