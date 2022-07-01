import { Link, Card, DataTable, Layout, SkeletonBodyText } from "@shopify/polaris";
import { Loading } from '@shopify/app-bridge-react';
import { useNavigate } from "react-router-dom";
import { useAppQuery } from "../hooks";
import { useState } from "react";

export function ProductCard() {
    // const [isLoading, setIsLoading] = useState(true);

    // useAppQuery({
    //     url: "api/products/update"
    // });

    // const {
    //     data2,
    //     refetch: refetchProducts,
    //     isRefetching: isRefetching
    // } = useAppQuery({
    //     url: "/api/products",
    //     // reactQueryOptions: {
        //     onSuccess: () => {
        //         setIsLoading(false);
        //     }
        // }
    // });

    // let rows = [];

    // data.forEach((dataPiece) => {
    //     rows.concat([
    //         [
    //             <Link
    //                 removeUnderline
    //                 url="https://dckaptraining.myshopify.com/admin/apps/php-training/product/${dataPiece.product_id}"
    //             >
    //                 dataPiece.title
    //             </Link>,
    //             dataPiece.vendor,
    //             dataPiece.type,
    //             dataPiece.price,
    //             dataPiece.has_only_default_variant
    //         ]
    //     ]);
    // });

    // const rows = [
    //     [
    //         <Link
    //             removeUnderline
    //             url="https://dckaptraining.myshopify.com/admin/apps/php-training/product/7734062186734"
    //         >
    //             High Priest
    //         </Link>,
    //         "Vendor",
    //         "Religious Leader",
    //         10,
    //         "false"
    //     ],
    // ];
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

    console.log(data);

    let rows = [];

    return (
        <Card>
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