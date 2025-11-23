import { ConnectorConfig, DataConnect, QueryRef, QueryPromise, MutationRef, MutationPromise } from 'firebase/data-connect';

export const connectorConfig: ConnectorConfig;

export type TimestampString = string;
export type UUIDString = string;
export type Int64String = string;
export type DateString = string;




export interface AlertConfiguration_Key {
  id: UUIDString;
  __typename?: 'AlertConfiguration_Key';
}

export interface CreateDetectionEventData {
  detectionEvent_insert: DetectionEvent_Key;
}

export interface CreateDetectionEventVariables {
  detectionRuleId: UUIDString;
  videoSourceId: UUIDString;
  boundingBox: string;
  confidence: number;
  detectedObject: string;
  timestamp: TimestampString;
}

export interface DetectionEvent_Key {
  id: UUIDString;
  __typename?: 'DetectionEvent_Key';
}

export interface DetectionModel_Key {
  id: UUIDString;
  __typename?: 'DetectionModel_Key';
}

export interface DetectionRule_Key {
  id: UUIDString;
  __typename?: 'DetectionRule_Key';
}

export interface ListDetectionRulesForVideoSourceData {
  detectionRules: ({
    id: UUIDString;
    name: string;
    detectionTarget: string;
    confidenceThreshold?: number | null;
    isActive: boolean;
  } & DetectionRule_Key)[];
}

export interface ListDetectionRulesForVideoSourceVariables {
  videoSourceId: UUIDString;
}

export interface ListVideoSourcesData {
  videoSources: ({
    id: UUIDString;
    name: string;
    sourceUrl: string;
    status: string;
  } & VideoSource_Key)[];
}

export interface UpdateVideoSourceStatusData {
  videoSource_update?: VideoSource_Key | null;
}

export interface UpdateVideoSourceStatusVariables {
  id: UUIDString;
  status: string;
}

export interface User_Key {
  id: UUIDString;
  __typename?: 'User_Key';
}

export interface VideoSource_Key {
  id: UUIDString;
  __typename?: 'VideoSource_Key';
}

interface CreateDetectionEventRef {
  /* Allow users to create refs without passing in DataConnect */
  (vars: CreateDetectionEventVariables): MutationRef<CreateDetectionEventData, CreateDetectionEventVariables>;
  /* Allow users to pass in custom DataConnect instances */
  (dc: DataConnect, vars: CreateDetectionEventVariables): MutationRef<CreateDetectionEventData, CreateDetectionEventVariables>;
  operationName: string;
}
export const createDetectionEventRef: CreateDetectionEventRef;

export function createDetectionEvent(vars: CreateDetectionEventVariables): MutationPromise<CreateDetectionEventData, CreateDetectionEventVariables>;
export function createDetectionEvent(dc: DataConnect, vars: CreateDetectionEventVariables): MutationPromise<CreateDetectionEventData, CreateDetectionEventVariables>;

interface ListVideoSourcesRef {
  /* Allow users to create refs without passing in DataConnect */
  (): QueryRef<ListVideoSourcesData, undefined>;
  /* Allow users to pass in custom DataConnect instances */
  (dc: DataConnect): QueryRef<ListVideoSourcesData, undefined>;
  operationName: string;
}
export const listVideoSourcesRef: ListVideoSourcesRef;

export function listVideoSources(): QueryPromise<ListVideoSourcesData, undefined>;
export function listVideoSources(dc: DataConnect): QueryPromise<ListVideoSourcesData, undefined>;

interface UpdateVideoSourceStatusRef {
  /* Allow users to create refs without passing in DataConnect */
  (vars: UpdateVideoSourceStatusVariables): MutationRef<UpdateVideoSourceStatusData, UpdateVideoSourceStatusVariables>;
  /* Allow users to pass in custom DataConnect instances */
  (dc: DataConnect, vars: UpdateVideoSourceStatusVariables): MutationRef<UpdateVideoSourceStatusData, UpdateVideoSourceStatusVariables>;
  operationName: string;
}
export const updateVideoSourceStatusRef: UpdateVideoSourceStatusRef;

export function updateVideoSourceStatus(vars: UpdateVideoSourceStatusVariables): MutationPromise<UpdateVideoSourceStatusData, UpdateVideoSourceStatusVariables>;
export function updateVideoSourceStatus(dc: DataConnect, vars: UpdateVideoSourceStatusVariables): MutationPromise<UpdateVideoSourceStatusData, UpdateVideoSourceStatusVariables>;

interface ListDetectionRulesForVideoSourceRef {
  /* Allow users to create refs without passing in DataConnect */
  (vars: ListDetectionRulesForVideoSourceVariables): QueryRef<ListDetectionRulesForVideoSourceData, ListDetectionRulesForVideoSourceVariables>;
  /* Allow users to pass in custom DataConnect instances */
  (dc: DataConnect, vars: ListDetectionRulesForVideoSourceVariables): QueryRef<ListDetectionRulesForVideoSourceData, ListDetectionRulesForVideoSourceVariables>;
  operationName: string;
}
export const listDetectionRulesForVideoSourceRef: ListDetectionRulesForVideoSourceRef;

export function listDetectionRulesForVideoSource(vars: ListDetectionRulesForVideoSourceVariables): QueryPromise<ListDetectionRulesForVideoSourceData, ListDetectionRulesForVideoSourceVariables>;
export function listDetectionRulesForVideoSource(dc: DataConnect, vars: ListDetectionRulesForVideoSourceVariables): QueryPromise<ListDetectionRulesForVideoSourceData, ListDetectionRulesForVideoSourceVariables>;

