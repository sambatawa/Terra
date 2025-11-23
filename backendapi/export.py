import argparse
import sys
from pathlib import Path
import shutil


def export_onnx(pt_path: Path, out_path: Path, imgsz: int = 640, opset: int = 12, dynamic: bool = True, simplify: bool = True) -> Path:
    if not pt_path.exists():
        print(f"ERROR: .pt not found at {pt_path}")
        sys.exit(1)
    try:
        from ultralytics import YOLO
    except Exception:
        print("ERROR: ultralytics is not installed. Install with: pip install ultralytics")
        sys.exit(1)

    out_path.parent.mkdir(parents=True, exist_ok=True)
    print(f"Exporting ONNX from {pt_path} -> {out_path} (imgsz={imgsz}, opset={opset}, dynamic={dynamic}, simplify={simplify})")

    model = YOLO(str(pt_path))
    result = model.export(
        format="onnx",
        imgsz=imgsz,
        opset=opset,
        dynamic=dynamic,
        simplify=simplify,
    )
    res_path = Path(result) if result else pt_path.with_suffix(".onnx")

    try:
        if res_path.resolve() != out_path.resolve():
            shutil.copy2(res_path, out_path)
    except FileNotFoundError:
        print(f"ERROR: Export output not found: {res_path}")
        sys.exit(1)

    print(f"Export complete: {out_path}")
    return out_path


def validate_onnx(onnx_path: Path) -> bool:
    try:
        import onnxruntime as ort
        _ = ort.InferenceSession(str(onnx_path), providers=["CPUExecutionProvider"])
        print("ONNX runtime can load the model")
        return True
    except Exception as e:
        print(f"Validation failed opening ONNX: {e}")
        return False


def main():
    base = Path(__file__).resolve().parent

    parser = argparse.ArgumentParser(description="Export YOLO best.pt to ONNX")
    parser.add_argument("--pt", type=str, default=str(base / "model" / "best.pt"), help="Path to source .pt")
    parser.add_argument("--out", type=str, default=str(base / "model" / "best.onnx"), help="Path to output .onnx")
    parser.add_argument("--imgsz", type=int, default=640, help="Image size for export")
    parser.add_argument("--opset", type=int, default=12, help="ONNX opset version")
    parser.add_argument("--no-dynamic", action="store_true", help="Disable dynamic axes")
    parser.add_argument("--no-simplify", action="store_true", help="Disable ONNX simplify")
    parser.add_argument("--validate", action="store_true", help="Validate ONNX by loading it with onnxruntime")

    args = parser.parse_args()

    pt = Path(args.pt)
    out = Path(args.out)

    onnx_path = export_onnx(
        pt_path=pt,
        out_path=out,
        imgsz=args.imgsz,
        opset=args.opset,
        dynamic=(not args.no_dynamic),
        simplify=(not args.no_simplify),
    )

    if args.validate:
        _ = validate_onnx(onnx_path)


if __name__ == "__main__":
    main()
