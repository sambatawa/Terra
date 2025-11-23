const { queryRef, executeQuery, mutationRef, executeMutation, validateArgs } = require('firebase/data-connect');

const connectorConfig = {
  connector: 'example',
  service: 'terra-sm5pro',
  location: 'us-east4'
};
exports.connectorConfig = connectorConfig;

const createDetectionEventRef = (dcOrVars, vars) => {
  const { dc: dcInstance, vars: inputVars} = validateArgs(connectorConfig, dcOrVars, vars, true);
  dcInstance._useGeneratedSdk();
  return mutationRef(dcInstance, 'CreateDetectionEvent', inputVars);
}
createDetectionEventRef.operationName = 'CreateDetectionEvent';
exports.createDetectionEventRef = createDetectionEventRef;

exports.createDetectionEvent = function createDetectionEvent(dcOrVars, vars) {
  return executeMutation(createDetectionEventRef(dcOrVars, vars));
};

const listVideoSourcesRef = (dc) => {
  const { dc: dcInstance} = validateArgs(connectorConfig, dc, undefined);
  dcInstance._useGeneratedSdk();
  return queryRef(dcInstance, 'ListVideoSources');
}
listVideoSourcesRef.operationName = 'ListVideoSources';
exports.listVideoSourcesRef = listVideoSourcesRef;

exports.listVideoSources = function listVideoSources(dc) {
  return executeQuery(listVideoSourcesRef(dc));
};

const updateVideoSourceStatusRef = (dcOrVars, vars) => {
  const { dc: dcInstance, vars: inputVars} = validateArgs(connectorConfig, dcOrVars, vars, true);
  dcInstance._useGeneratedSdk();
  return mutationRef(dcInstance, 'UpdateVideoSourceStatus', inputVars);
}
updateVideoSourceStatusRef.operationName = 'UpdateVideoSourceStatus';
exports.updateVideoSourceStatusRef = updateVideoSourceStatusRef;

exports.updateVideoSourceStatus = function updateVideoSourceStatus(dcOrVars, vars) {
  return executeMutation(updateVideoSourceStatusRef(dcOrVars, vars));
};

const listDetectionRulesForVideoSourceRef = (dcOrVars, vars) => {
  const { dc: dcInstance, vars: inputVars} = validateArgs(connectorConfig, dcOrVars, vars, true);
  dcInstance._useGeneratedSdk();
  return queryRef(dcInstance, 'ListDetectionRulesForVideoSource', inputVars);
}
listDetectionRulesForVideoSourceRef.operationName = 'ListDetectionRulesForVideoSource';
exports.listDetectionRulesForVideoSourceRef = listDetectionRulesForVideoSourceRef;

exports.listDetectionRulesForVideoSource = function listDetectionRulesForVideoSource(dcOrVars, vars) {
  return executeQuery(listDetectionRulesForVideoSourceRef(dcOrVars, vars));
};
