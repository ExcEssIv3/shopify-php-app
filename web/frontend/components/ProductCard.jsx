import { Link, Card, DataTable, SkeletonBodyText } from "@shopify/polaris";
import { Loading } from '@shopify/app-bridge-react';
import { useAppQuery } from "../hooks";

export function ProductCard() {
    const {
        data,
        isLoading,
        isRefetching
    } = useAppQuery({
        url: '/api/products'
    });

    if (isLoading || isRefetching) {
        return (
            <Card sectioned title="Product">
                <Loading />
                <SkeletonBodyText />
            </Card>
        );
        
    }

    let rows = [];

    data.forEach((dataPiece) => {
        const url = `https://dckaptraining.myshopify.com/admin/apps/php-training/product/${dataPiece.product_id}`
        rows.push([
            <Link
                removeUnderline
                url={url}
            >
                {dataPiece.title}
            </Link>,
            dataPiece.vendor,
            dataPiece.type,
            dataPiece.price,
            dataPiece.has_only_default_variant
        ])
    });

    return (
        <Card sectioned title="Products">
            <DataTable
                columnContentTypes = {[
                    "text",
                    "text",
                    "text",
                    "numeric",
                    "text"
                ]}
                headings={[
                    "Title",
                    "Vendor",
                    "Type",
                    "Price",
                    "Has only default variant"
                ]}
                rows={rows}
                loading={isLoading}
            />
        </Card>
    )
}