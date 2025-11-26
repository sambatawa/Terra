import cv2
import numpy as np
from main import predict_with_onnx 

def draw_boxes(frame, detections):
    for det in detections:
        x1, y1, x2, y2, conf, cls = det[:6]
        cv2.rectangle(frame, (int(x1), int(y1)), (int(x2), int(y2)), (0, 255, 0), 2)
        cv2.putText(frame, f"{cls}: {conf:.2f}", (int(x1), int(y1) - 5),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)

def run_webcam():
    webcam = cv2.VideoCapture(0)

    while True:
        ret, frame = webcam.read()
        if not ret:
            break
        detections = predict_with_onnx(frame) 
        draw_boxes(frame, detections)
        cv2.imshow("Realtime Detection", frame)
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    webcam.release()
    cv2.destroyAllWindows()

if __name__ == "__main__":
    run_webcam()
